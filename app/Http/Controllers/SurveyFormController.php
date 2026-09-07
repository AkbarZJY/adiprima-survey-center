<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SurveyFormController extends Controller
{
    public function showForm($slug)
    {
        $survey = Survey::where('slug', $slug)
            ->with(['activePeriod', 'questions.dimensionModel'])
            ->firstOrFail();

        $user = Auth::user();

        // Check if survey is active and within valid dates
        if (!$survey->is_active) {
            return view('survey.inactive', [
                'survey' => $survey,
                'message' => 'Kuesioner ini sedang dinonaktifkan oleh administrator.',
            ]);
        }

        $today = Carbon::today();
        if ($survey->start_date && $today->lt($survey->start_date)) {
            return view('survey.inactive', [
                'survey' => $survey,
                'message' => 'Kuesioner ini belum dimulai. Periode survei baru akan dibuka pada ' . $survey->start_date->isoFormat('D MMMM Y') . '.',
            ]);
        }

        if ($survey->end_date && $today->gt($survey->end_date)) {
            return view('survey.inactive', [
                'survey' => $survey,
                'message' => 'Masa aktif kuesioner ini telah berakhir pada ' . $survey->end_date->isoFormat('D MMMM Y') . '.',
            ]);
        }

        $period = $survey->getOrCreateCurrentPeriod();

        // Check if user has already submitted response for this period
        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('survey_period_id', $period->id)
            ->where('user_id', $user->id)
            ->first();

        // Standard organizational demographics
        $departments = [
            'HRGA', 'PPIC', 'QC', 'UTILITY', 'R&D',
            'PM1', 'PM2', 'PM3', 'TEKNIK', 'PP',
            'MRK', 'PCH', 'IT', 'FA', 'IA'
        ];

        $positions = [
            'General Manager', 'Manager', 'Ast. Manager',
            'Supervisor', 'Ast. Supervisor', 'Staf', 'Karu', 'Operator'
        ];

        $tenures = [
            'kurang dari 1 tahun', '1 - 5 tahun', '6 - 10 tahun',
            '11 - 15 tahun', '16 - 20 tahun', '21 - 25 tahun', '> 25 tahun'
        ];

        $educations = [
            'SD', 'SMP Sederajat', 'SMA/ SMK Sederjat',
            'D1 - D2 - D3 - D4', 'S1', 'S2 ke atas'
        ];

        // Group questions by dimension and section
        $questions = $survey->questions->sortBy('order');
        $sectionBQuestions = $questions->where('section', 'B');
        $sectionCQuestions = $questions->where('section', 'C');
        $questionsByDimension = $questions->groupBy('dimensionName');

        return view('survey.form', compact(
            'survey', 'period', 'user', 'existingResponse',
            'departments', 'positions', 'tenures', 'educations',
            'questions', 'sectionBQuestions', 'sectionCQuestions', 'questionsByDimension'
        ));
    }

    public function store(Request $request, $slug)
    {
        $survey = Survey::where('slug', $slug)->with(['activePeriod', 'questions'])->firstOrFail();
        $user = Auth::user();

        if (!$survey->isWithinActiveDate()) {
            return back()->with('error', 'Kuesioner tidak aktif atau telah melewati batas masa pengisian.');
        }

        $period = $survey->getOrCreateCurrentPeriod();

        // Validation for Demographics
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|string',
            'age' => 'required|numeric|min:17|max:80',
            'education' => 'required|string',
            'employment_status' => 'required|string',
            'tenure' => 'required|string',
            'department' => 'required|string',
            'position' => 'required|string',
        ]);

        $isPermanent = strtolower($request->employment_status) === 'tetap';
        $leadershipPositions = ['General Manager', 'Manager', 'Ast. Manager', 'Supervisor', 'Ast. Supervisor', 'Karu'];
        $isLeadership = in_array($request->position, $leadershipPositions);

        // Save or update survey response header
        $response = SurveyResponse::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'survey_period_id' => $period->id,
                'user_id' => $user->id,
            ],
            [
                'nik' => $user->nik,
                'name' => $request->name,
                'gender' => $request->gender,
                'age' => $request->age,
                'education' => $request->education,
                'employment_status' => $request->employment_status,
                'tenure' => $request->tenure,
                'department' => $request->department,
                'position' => $request->position,
                'submitted_at' => Carbon::now(),
                'ip_address' => $request->ip(),
            ]
        );

        // Clear previous answers if re-submitting
        SurveyAnswer::where('survey_response_id', $response->id)->delete();

        // Dynamic processing for all questions in this survey
        foreach ($survey->questions as $question) {
            // Check position/status criteria if set on question
            if ($question->applies_to_employment_status && strtolower($question->applies_to_employment_status) === 'tetap' && !$isPermanent) {
                continue;
            }
            if ($question->applies_to_positions && str_contains(strtolower($question->applies_to_positions), 'karu') && !$isLeadership) {
                continue;
            }

            // Handle Question Types
            if ($question->question_type === 'dual_rating' || $question->section === 'B') {
                $expScore = $request->input("expectation_" . $question->id);
                $realScore = $request->input("reality_" . $question->id);
                $reasonText = $request->input("reason_" . $question->id);

                $threshold = $question->low_score_threshold ?? 2;
                $saveReason = ($question->require_reason_on_low_score && $realScore <= $threshold) ? $reasonText : ($realScore <= 2 ? $reasonText : null);

                SurveyAnswer::create([
                    'survey_response_id' => $response->id,
                    'question_id' => $question->id,
                    'expectation_score' => $expScore,
                    'reality_score' => $realScore,
                    'reason_text' => $saveReason,
                ]);
            } elseif ($question->question_type === 'single_rating') {
                $score = $request->input("single_rating_" . $question->id) ?? $request->input("general_" . $question->id) ?? $request->input("reality_" . $question->id);
                $reasonText = $request->input("reason_" . $question->id);

                $threshold = $question->low_score_threshold ?? 2;
                $saveReason = ($question->require_reason_on_low_score && $score <= $threshold) ? $reasonText : null;

                SurveyAnswer::create([
                    'survey_response_id' => $response->id,
                    'question_id' => $question->id,
                    'reality_score' => $score,
                    'reason_text' => $saveReason,
                ]);
            } elseif ($question->question_type === 'essay') {
                $textAnswer = $request->input("essay_" . $question->id) ?? $request->input("general_reason_" . $question->id) ?? $request->input("text_answer_" . $question->id);

                SurveyAnswer::create([
                    'survey_response_id' => $response->id,
                    'question_id' => $question->id,
                    'text_answer' => $textAnswer,
                ]);
            } elseif ($question->question_type === 'multiple_choice') {
                $choice = $request->input("choice_" . $question->id);

                SurveyAnswer::create([
                    'survey_response_id' => $response->id,
                    'question_id' => $question->id,
                    'text_answer' => $choice,
                ]);
            }
        }

        return redirect()->route('survey.success', ['slug' => $survey->slug]);
    }

    public function success($slug)
    {
        $survey = Survey::where('slug', $slug)->firstOrFail();
        return view('survey.success', compact('survey'));
    }
}
