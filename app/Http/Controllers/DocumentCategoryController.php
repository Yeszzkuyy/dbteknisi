<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::withTrashed()
            ->orderBy('name')
            ->get();

        return view('document_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('document_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:document_categories,name',
        ]);

        DocumentCategory::create($validated);

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Kategori "' . $validated['name'] . '" berhasil ditambahkan');
    }

    public function edit(DocumentCategory $documentCategory)
    {
        return view('document_categories.edit', compact('documentCategory'));
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:document_categories,name,' . $documentCategory->id,
        ]);

        $documentCategory->update($validated);

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(DocumentCategory $documentCategory)
    {
        $documentCategory->delete();

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Kategori "' . $documentCategory->name . '" berhasil dihapus');
    }

    public function restore(int $id)
    {
        $category = DocumentCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil direstore');
    }
}