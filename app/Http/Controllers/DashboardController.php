<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyPeriod;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyDimension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. All available surveys for dropdown switcher
        $allSurveys = Survey::withCount(['responses', 'questions'])->orderBy('title')->get();

        // Selected Survey
        $surveyId = $request->get('survey_id');
        if ($surveyId) {
            $activeSurvey = Survey::find($surveyId);
        }
        if (empty($activeSurvey)) {
            $activeSurvey = Survey::where('slug', 'engagement-survey')->first() ?? Survey::first();
        }

        if (!$activeSurvey) {
            return view('admin.dashboard.empty', ['allSurveys' => $allSurveys]);
        }

        $period = $activeSurvey->activePeriod;

        $tab = $request->get('tab', 'raw_data'); // raw_data, rekap_perolehan, analisa, descriptive_reasons
        $search = $request->get('search');
        $departmentFilter = $request->get('department');
        $positionFilter = $request->get('position');

        // 2. Raw Data Query
        $responsesQuery = SurveyResponse::where('survey_id', $activeSurvey->id)
            ->with(['answers.question', 'user'])
            ->orderBy('submitted_at', 'desc');

        if ($search) {
            $responsesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($departmentFilter) {
            $responsesQuery->where('department', $departmentFilter);
        }

        if ($positionFilter) {
            $responsesQuery->where('position', $positionFilter);
        }

        $responses = $responsesQuery->paginate(10)->withQueryString();

        // 3. Questions list for headers and analytics
        $questions = SurveyQuestion::where('survey_id', $activeSurvey->id)
            ->with('dimensionModel')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        // 4. Rekap Perolehan (Department Participation Monitoring)
        $departmentTargets = [
            'Produksi' => ['target' => 150, 'depts' => ['PM1', 'PM2', 'PM3', 'TEKNIK', 'UTILITY']],
            'Marketing' => ['target' => 30, 'depts' => ['MRK', 'PPIC']],
            'Finance' => ['target' => 20, 'depts' => ['FA', 'PCH', 'IA']],
            'HRGA' => ['target' => 15, 'depts' => ['HRGA', 'PP']],
            'IT & Tech' => ['target' => 10, 'depts' => ['IT', 'R&D', 'QC']],
        ];

        $rekapData = [];
        $totalTarget = 225;
        $totalFilled = SurveyResponse::where('survey_id', $activeSurvey->id)->count();

        foreach ($departmentTargets as $groupName => $info) {
            $count = SurveyResponse::where('survey_id', $activeSurvey->id)
                ->whereIn('department', $info['depts'])
                ->count();
            $target = $info['target'];
            $pct = $target > 0 ? round(($count / $target) * 100) : 0;
            $rekapData[] = [
                'group' => $groupName,
                'filled' => $count,
                'target' => $target,
                'progress' => $pct,
            ];
        }

        $overallProgressPct = $totalTarget > 0 ? round(($totalFilled / $totalTarget) * 100, 1) : 0;

        // 5. Dynamic Dimension Analytics
        // Extract distinct dimensions from this survey's questions
        $surveyDimensions = $questions->pluck('dimensionName')->unique()->filter()->values()->all();
        if (empty($surveyDimensions)) {
            $surveyDimensions = ['General'];
        }

        $dimensionScores = [];
        $dimensionExpectationScores = [];
        $dimensionGaps = [];

        foreach ($surveyDimensions as $dim) {
            $stats = DB::table('survey_answers')
                ->join('survey_questions', 'survey_answers.question_id', '=', 'survey_questions.id')
                ->join('survey_responses', 'survey_answers.survey_response_id', '=', 'survey_responses.id')
                ->where('survey_responses.survey_id', $activeSurvey->id)
                ->where(function ($q) use ($dim) {
                    $q->where('survey_questions.dimension', $dim)
                      ->orWhereExists(function ($sub) use ($dim) {
                          $sub->select(DB::raw(1))
                              ->from('survey_dimensions')
                              ->whereColumn('survey_dimensions.id', 'survey_questions.dimension_id')
                              ->where('survey_dimensions.name', $dim);
                      });
                })
                ->selectRaw('AVG(reality_score) as avg_real, AVG(expectation_score) as avg_exp')
                ->first();

            $realScore = round($stats->avg_real ?? 0, 2);
            $expScore = round($stats->avg_exp ?? 0, 2);

            $dimensionScores[$dim] = $realScore;
            $dimensionExpectationScores[$dim] = $expScore;
            $dimensionGaps[$dim] = round($expScore - $realScore, 2);
        }

        $overallEngagementScore = count($dimensionScores) > 0 ? round(array_sum($dimensionScores) / count($dimensionScores), 2) : 0;

        // Highest and Lowest Dimension
        $highestDimension = '-';
        $highestScore = 0;
        $lowestDimension = '-';
        $lowestScore = 999;

        foreach ($dimensionScores as $dim => $score) {
            if ($score >= $highestScore) {
                $highestScore = $score;
                $highestDimension = $dim;
            }
            if ($score <= $lowestScore && $score > 0) {
                $lowestScore = $score;
                $lowestDimension = $dim;
            }
        }
        if ($lowestScore === 999) {
            $lowestScore = 0;
        }

        // 6. Descriptive Answers / Low-score reasons collection
        $lowScoreReasons = SurveyAnswer::whereHas('response', function ($q) use ($activeSurvey) {
                $q->where('survey_id', $activeSurvey->id);
            })
            ->whereNotNull('reason_text')
            ->where('reason_text', '!=', '')
            ->with(['question', 'response'])
            ->latest()
            ->take(50)
            ->get();

        $essayAnswers = SurveyAnswer::whereHas('response', function ($q) use ($activeSurvey) {
                $q->where('survey_id', $activeSurvey->id);
            })
            ->whereNotNull('text_answer')
            ->where('text_answer', '!=', '')
            ->with(['question', 'response'])
            ->latest()
            ->take(50)
            ->get();

        // 7. Filter Options
        $allDepartments = [
            'HRGA', 'PPIC', 'QC', 'UTILITY', 'R&D',
            'PM1', 'PM2', 'PM3', 'TEKNIK', 'PP',
            'MRK', 'PCH', 'IT', 'FA', 'IA'
        ];
        $allPositions = [
            'General Manager', 'Manager', 'Ast. Manager',
            'Supervisor', 'Ast. Supervisor', 'Staf', 'Karu', 'Operator'
        ];

        return view('admin.dashboard.index', compact(
            'allSurveys', 'activeSurvey', 'period', 'tab', 'search', 'departmentFilter', 'positionFilter',
            'responses', 'questions', 'rekapData', 'totalTarget', 'totalFilled', 'overallProgressPct',
            'surveyDimensions', 'dimensionScores', 'dimensionExpectationScores', 'dimensionGaps',
            'overallEngagementScore', 'highestDimension', 'highestScore', 'lowestDimension', 'lowestScore',
            'lowScoreReasons', 'essayAnswers', 'allDepartments', 'allPositions'
        ));
    }

    public function exportCsv(Request $request)
    {
        $surveyId = $request->get('survey_id');
        if ($surveyId) {
            $activeSurvey = Survey::findOrFail($surveyId);
        } else {
            $activeSurvey = Survey::where('slug', 'engagement-survey')->first() ?? Survey::firstOrFail();
        }

        $questions = SurveyQuestion::where('survey_id', $activeSurvey->id)
            ->orderBy('order')
            ->get();

        $responses = SurveyResponse::where('survey_id', $activeSurvey->id)
            ->with(['answers.question'])
            ->get();

        $fileName = "raw_data_" . Str::slug($activeSurvey->title) . "_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($responses, $questions) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            $headerRow = ['No', 'NIK', 'Nama', 'Jenis Kelamin', 'Usia', 'Pendidikan', 'Status Kepegawaian', 'Lama Bekerja', 'Departemen', 'Jabatan', 'Waktu Submit'];
            foreach ($questions as $q) {
                if ($q->question_type === 'dual_rating') {
                    $headerRow[] = 'Q' . $q->question_number . ' - ' . ($q->indicator_title ?? $q->dimension) . ' (Harapan)';
                    $headerRow[] = 'Q' . $q->question_number . ' - ' . ($q->indicator_title ?? $q->dimension) . ' (Kenyataan)';
                    $headerRow[] = 'Q' . $q->question_number . ' (Alasan Nilai Rendah)';
                } elseif ($q->question_type === 'essay') {
                    $headerRow[] = 'Q' . $q->question_number . ' - ' . ($q->indicator_title ?? 'Uraian');
                } else {
                    $headerRow[] = 'Q' . $q->question_number . ' - ' . ($q->indicator_title ?? $q->dimension) . ' (Skor)';
                }
            }
            fputcsv($file, $headerRow);

            $no = 1;
            foreach ($responses as $resp) {
                $answersMap = $resp->answers->keyBy('question_id');
                $row = [
                    $no++,
                    $resp->nik,
                    $resp->name,
                    $resp->gender,
                    $resp->age,
                    $resp->education,
                    $resp->employment_status,
                    $resp->tenure,
                    $resp->department,
                    $resp->position,
                    $resp->submitted_at ? $resp->submitted_at->format('Y-m-d H:i') : '-',
                ];

                foreach ($questions as $q) {
                    $ans = $answersMap->get($q->id);
                    if ($q->question_type === 'dual_rating') {
                        $row[] = $ans ? ($ans->expectation_score ?? '-') : '-';
                        $row[] = $ans ? ($ans->reality_score ?? '-') : '-';
                        $row[] = $ans ? ($ans->reason_text ?? '-') : '-';
                    } elseif ($q->question_type === 'essay') {
                        $row[] = $ans ? ($ans->text_answer ?? '-') : '-';
                    } else {
                        $row[] = $ans ? ($ans->reality_score ?? $ans->text_answer ?? '-') : '-';
                    }
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
