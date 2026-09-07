<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionTemplate;
use App\Models\SurveyDimension;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $dimensionFilter = $request->get('dimension_id');
        $typeFilter = $request->get('question_type');
        $search = $request->get('search');

        $query = QuestionTemplate::with('dimension')->orderBy('order')->orderBy('id');

        if ($dimensionFilter) {
            $query->where('dimension_id', $dimensionFilter);
        }

        if ($typeFilter) {
            $query->where('question_type', $typeFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('indicator_title', 'like', "%{$search}%");
            });
        }

        $templates = $query->paginate(15)->withQueryString();
        $dimensions = SurveyDimension::orderBy('order')->get();

        return view('admin.question_bank.index', compact('templates', 'dimensions', 'dimensionFilter', 'typeFilter', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dimension_id' => 'nullable|exists:survey_dimensions,id',
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

        $optionsJson = null;
        if ($request->question_type === 'multiple_choice' && $request->options_text) {
            $options = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $optionsJson = array_values($options);
        }

        QuestionTemplate::create([
            'dimension_id' => $request->dimension_id,
            'indicator_title' => $request->indicator_title,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'rating_scale' => $request->rating_scale ?? 4,
            'require_reason_on_low_score' => $request->has('require_reason_on_low_score'),
            'low_score_threshold' => $request->low_score_threshold ?? 2,
            'options_json' => $optionsJson,
            'applies_to_employment_status' => $request->applies_to_employment_status,
            'applies_to_positions' => $request->applies_to_positions,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.question-bank.index')->with('success', 'Template pertanyaan berhasil ditambahkan ke Bank Soal.');
    }

    public function update(Request $request, $id)
    {
        $template = QuestionTemplate::findOrFail($id);

        $request->validate([
            'dimension_id' => 'nullable|exists:survey_dimensions,id',
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

        $optionsJson = $template->options_json;
        if ($request->question_type === 'multiple_choice' && $request->options_text) {
            $options = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $optionsJson = array_values($options);
        }

        $template->update([
            'dimension_id' => $request->dimension_id,
            'indicator_title' => $request->indicator_title,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'rating_scale' => $request->rating_scale ?? 4,
            'require_reason_on_low_score' => $request->has('require_reason_on_low_score'),
            'low_score_threshold' => $request->low_score_threshold ?? 2,
            'options_json' => $optionsJson,
            'applies_to_employment_status' => $request->applies_to_employment_status,
            'applies_to_positions' => $request->applies_to_positions,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.question-bank.index')->with('success', 'Template pertanyaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $template = QuestionTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.question-bank.index')->with('success', 'Template pertanyaan berhasil dihapus dari Bank Soal.');
    }
}
