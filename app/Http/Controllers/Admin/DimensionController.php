<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyDimension;
use Illuminate\Http\Request;

class DimensionController extends Controller
{
    public function index()
    {
        $dimensions = SurveyDimension::withCount(['questions', 'questionTemplates'])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('admin.dimensions.index', compact('dimensions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'color' => 'required|string|max:30',
            'order' => 'nullable|numeric',
        ]);

        SurveyDimension::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'color' => $request->color ?? '#2563EB',
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.dimensions.index')->with('success', 'Dimensi/Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dimension = SurveyDimension::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'color' => 'required|string|max:30',
            'order' => 'nullable|numeric',
        ]);

        $dimension->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'color' => $request->color,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.dimensions.index')->with('success', 'Dimensi/Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dimension = SurveyDimension::findOrFail($id);
        $dimension->delete();

        return redirect()->route('admin.dimensions.index')->with('success', 'Dimensi/Kategori berhasil dihapus.');
    }
}
