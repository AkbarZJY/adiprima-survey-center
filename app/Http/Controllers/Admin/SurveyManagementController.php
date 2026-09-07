<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyPeriod;
use App\Models\SurveyDimension;
use App\Models\QuestionTemplate;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SurveyManagementController extends Controller
{
    public function index()
    {
        $surveys = Survey::withCount(['questions', 'responses'])
            ->with('activePeriod')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->title);
        $count = Survey::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $survey = Survey::create([
            'title' => $request->title,
            'slug' => $slug,
            'category' => $request->category,
            'description' => $request->description,
            'icon' => $request->icon ?? 'bi-clipboard-data',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        // Create initial survey period
        SurveyPeriod::create([
            'survey_id' => $survey->id,
            'period_name' => 'Periode ' . date('Y'),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.surveys.questions', $survey->id)->with('success', 'Survei berhasil dibuat. Sekarang Anda dapat menambahkan pertanyaan ke kuesioner ini.');
    }

    public function edit($id)
    {
        $survey = Survey::with('activePeriod')->findOrFail($id);
        return view('admin.surveys.edit', compact('survey'));
    }

    public function update(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $survey->update([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'icon' => $request->icon ?? $survey->icon,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        // Update active period
        $period = $survey->activePeriod;
        if ($period) {
            $period->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->has('is_active'),
            ]);
        } else {
            SurveyPeriod::create([
                'survey_id' => $survey->id,
                'period_name' => 'Periode ' . date('Y'),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->has('is_active'),
            ]);
        }

        return redirect()->route('admin.surveys.index')->with('success', 'Data survei dan masa aktif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();

        return redirect()->route('admin.surveys.index')->with('success', 'Survei berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->is_active = !$survey->is_active;
        $survey->save();

        if ($survey->activePeriod) {
            $survey->activePeriod->is_active = $survey->is_active;
            $survey->activePeriod->save();
        }

        return back()->with('success', 'Status survei berhasil diubah.');
    }

    /**
     * Question Builder for this Survey
     */
    public function questions($id)
    {
        $survey = Survey::with(['questions.dimensionModel', 'questions.template', 'activePeriod'])->findOrFail($id);
        $dimensions = SurveyDimension::orderBy('order')->get();
        $bankTemplates = QuestionTemplate::with('dimension')->orderBy('order')->get();

        // Existing questions mapped by dimension
        $questions = $survey->questions->sortBy('order');

        return view('admin.surveys.questions', compact('survey', 'dimensions', 'bankTemplates', 'questions'));
    }

    /**
     * Add single question directly
     */
    public function storeQuestion(Request $request, $surveyId)
    {
        $survey = Survey::findOrFail($surveyId);

        $request->validate([
            'dimension_id' => 'nullable|exists:survey_dimensions,id',
            'section' => 'required|string|max:10',
            'indicator_title' => 'nullable|string|max:255',
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:dual_rating,single_rating,multiple_choice,essay',
            'rating_scale' => 'required|numeric|in:4,5,10',
            'require_reason_on_low_score' => 'nullable|boolean',
            'low_score_threshold' => 'nullable|numeric|min:1|max:10',
            'applies_to_employment_status' => 'nullable|string|max:100',
            'applies_to_positions' => 'nullable|string|max:100',
            'order' => 'nullable|numeric',
        ]);

        $dimension = $request->dimension_id ? SurveyDimension::find($request->dimension_id) : null;
        $maxNum = SurveyQuestion::where('survey_id', $survey->id)->where('section', $request->section)->max('question_number') ?? 0;
        $maxOrder = SurveyQuestion::where('survey_id', $survey->id)->max('order') ?? 0;

        $optionsJson = null;
        if ($request->question_type === 'multiple_choice' && $request->options_text) {
            $options = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $optionsJson = array_values($options);
        }

        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'dimension_id' => $request->dimension_id,
            'dimension' => $dimension ? $dimension->name : 'General',
            'section' => $request->section,
            'question_number' => $maxNum + 1,
            'indicator_title' => $request->indicator_title,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'rating_scale' => $request->rating_scale ?? 4,
            'require_reason_on_low_score' => $request->has('require_reason_on_low_score'),
            'low_score_threshold' => $request->low_score_threshold ?? 2,
            'options_json' => $optionsJson,
            'applies_to_employment_status' => $request->applies_to_employment_status,
            'applies_to_positions' => $request->applies_to_positions,
            'order' => $request->order ?? ($maxOrder + 1),
        ]);

        return back()->with('success', 'Pertanyaan baru berhasil ditambahkan ke survei.');
    }

    /**
     * Import multiple selected questions from Question Bank Template
     */
    public function importFromBank(Request $request, $surveyId)
    {
        $survey = Survey::findOrFail($surveyId);

        $request->validate([
            'template_ids' => 'required|array',
            'template_ids.*' => 'exists:question_templates,id',
            'section' => 'required|string|max:10',
        ]);

        $templates = QuestionTemplate::whereIn('id', $request->template_ids)->with('dimension')->get();
        $maxNum = SurveyQuestion::where('survey_id', $survey->id)->where('section', $request->section)->max('question_number') ?? 0;
        $maxOrder = SurveyQuestion::where('survey_id', $survey->id)->max('order') ?? 0;

        $count = 0;
        foreach ($templates as $template) {
            $maxNum++;
            $maxOrder++;

            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'dimension_id' => $template->dimension_id,
                'question_template_id' => $template->id,
                'dimension' => $template->dimension?->name ?? 'General',
                'section' => $request->section,
                'question_number' => $maxNum,
                'indicator_title' => $template->indicator_title,
                'question_text' => $template->question_text,
                'question_type' => $template->question_type,
                'rating_scale' => $template->rating_scale,
                'require_reason_on_low_score' => $template->require_reason_on_low_score,
                'low_score_threshold' => $template->low_score_threshold,
                'options_json' => $template->options_json,
                'applies_to_employment_status' => $template->applies_to_employment_status,
                'applies_to_positions' => $template->applies_to_positions,
                'order' => $maxOrder,
            ]);
            $count++;
        }

        return back()->with('success', "Berhasil menambahkan {$count} pertanyaan dari Bank Template ke kuesioner.");
    }

    /**
     * Update question in survey
     */
    public function updateQuestion(Request $request, $surveyId, $questionId)
    {
        $survey = Survey::findOrFail($surveyId);
        $question = SurveyQuestion::where('survey_id', $survey->id)->findOrFail($questionId);

        $request->validate([
            'dimension_id' => 'nullable|exists:survey_dimensions,id',
            'section' => 'required|string|max:10',
            'question_number' => 'required|numeric',
            'indicator_title' => 'nullable|string|max:255',
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:dual_rating,single_rating,multiple_choice,essay',
            'rating_scale' => 'required|numeric|in:4,5,10',
            'require_reason_on_low_score' => 'nullable|boolean',
            'low_score_threshold' => 'nullable|numeric|min:1|max:10',
            'applies_to_employment_status' => 'nullable|string|max:100',
            'applies_to_positions' => 'nullable|string|max:100',
            'order' => 'nullable|numeric',
        ]);

        $dimension = $request->dimension_id ? SurveyDimension::find($request->dimension_id) : null;

        $optionsJson = $question->options_json;
        if ($request->question_type === 'multiple_choice' && $request->options_text) {
            $options = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $optionsJson = array_values($options);
        }

        $question->update([
            'dimension_id' => $request->dimension_id,
            'dimension' => $dimension ? $dimension->name : $question->dimension,
            'section' => $request->section,
            'question_number' => $request->question_number,
            'indicator_title' => $request->indicator_title,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'rating_scale' => $request->rating_scale ?? 4,
            'require_reason_on_low_score' => $request->has('require_reason_on_low_score'),
            'low_score_threshold' => $request->low_score_threshold ?? 2,
            'options_json' => $optionsJson,
            'applies_to_employment_status' => $request->applies_to_employment_status,
            'applies_to_positions' => $request->applies_to_positions,
            'order' => $request->order ?? $question->order,
        ]);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Remove question from survey
     */
    public function destroyQuestion($surveyId, $questionId)
    {
        $survey = Survey::findOrFail($surveyId);
        $question = SurveyQuestion::where('survey_id', $survey->id)->findOrFail($questionId);
        $question->delete();

        return back()->with('success', 'Pertanyaan berhasil dihapus dari survei.');
    }
}
