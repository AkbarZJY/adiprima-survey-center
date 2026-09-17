<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyDimension;
use App\Models\SurveyCategory;
use Illuminate\Http\Request;

class DimensionController extends Controller
{
    public function index(Request $request)
    {
        $categoryFilter = $request->get('category'); // category id or slug

        $categories = SurveyCategory::withCount('dimensions')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $query = SurveyDimension::with(['category'])
            ->withCount(['questions', 'questionTemplates'])
            ->orderBy('order')
            ->orderBy('id');

        if ($categoryFilter && $categoryFilter !== 'all') {
            if (is_numeric($categoryFilter)) {
                $query->where('survey_category_id', $categoryFilter);
            } else {
                $query->whereHas('category', function ($q) use ($categoryFilter) {
                    $q->where('slug', $categoryFilter);
                });
            }
        }

        $dimensions = $query->get();

        return view('admin.dimensions.index', compact('dimensions', 'categories', 'categoryFilter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'survey_category_id' => 'required|exists:survey_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'color' => 'required|string|max:30',
            'order' => 'nullable|numeric',
        ]);

        SurveyDimension::create([
            'survey_category_id' => $request->survey_category_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'color' => $request->color ?? '#2563EB',
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.dimensions.index', ['category' => $request->survey_category_id])
            ->with('success', 'Dimensi pertanyaan berhasil ditambahkan ke kategori yang dipilih.');
    }

    public function update(Request $request, $id)
    {
        $dimension = SurveyDimension::findOrFail($id);

        $request->validate([
            'survey_category_id' => 'required|exists:survey_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'color' => 'required|string|max:30',
            'order' => 'nullable|numeric',
        ]);

        $dimension->update([
            'survey_category_id' => $request->survey_category_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'color' => $request->color,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.dimensions.index', ['category' => $request->survey_category_id])
            ->with('success', 'Dimensi pertanyaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dimension = SurveyDimension::findOrFail($id);
        $catId = $dimension->survey_category_id;
        $dimension->delete();

        return redirect()->route('admin.dimensions.index', ['category' => $catId])
            ->with('success', 'Dimensi berhasil dihapus.');
    }
}
