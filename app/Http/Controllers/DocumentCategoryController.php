<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::latest()->get();
        return view('settings.document-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('settings.document-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DocumentCategory::create($validated);

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Document Category berhasil ditambahkan.');
    }

    public function edit(DocumentCategory $documentCategory)
    {
        return view('settings.document-categories.edit', compact('documentCategory'));
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $documentCategory->update($validated);

        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Document Category berhasil diupdate.');
    }

    public function destroy(DocumentCategory $documentCategory)
    {
        $documentCategory->delete();
        return redirect()
            ->route('document-categories.index')
            ->with('success', 'Document Category berhasil dihapus.');
    }
}