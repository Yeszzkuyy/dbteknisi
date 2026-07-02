<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectDocumentController extends Controller
{
    public function create(Project $project)
    {
        $categories = DocumentCategory::orderBy('name')->get();

        return view(
            'project_documents.create',
            compact('project', 'categories')
        );
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'document_category_id' => 'required|exists:document_categories,id',
            'file'                 => 'required|file|max:10240',
            'notes'                => 'nullable',
        ]);

        $file = $request->file('file');
        $path = $file->store('project-documents', 'public');

        $project->documents()->create([
            'document_category_id' => $validated['document_category_id'],
            'file_name'            => $file->getClientOriginalName(),
            'file_path'            => $path,
            'notes'                => $validated['notes'] ?? null,
            'uploaded_by'          => Auth::id(),
        ]);

        ProjectActivity::create([
            'project_id'    => $project->id,
            'user_id'       => auth()->id(),
            'activity_date' => now(),
            'title'         => 'Dokumen Diupload',
            'description'   => $file->getClientOriginalName(),
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Dokumen berhasil diupload');
    }

    public function show(ProjectDocument $projectDocument)
    {
        $projectDocument->load(['project', 'uploader', 'category']);

        return view(
            'project_documents.show',
            compact('projectDocument')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectDocument $projectDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectDocument $projectDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectDocument $projectDocument)
    {
        //
    }
}
