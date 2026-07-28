<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use Illuminate\Http\Request;

class ProjectStatusController extends Controller
{
    public function index()
    {
        $statuses = ProjectStatus::latest()->get();
        return view('settings.project-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('settings.project-statuses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        ProjectStatus::create($validated);

        return redirect()
            ->route('project-statuses.index')
            ->with('success', 'Project Status berhasil ditambahkan.');
    }

    public function edit(ProjectStatus $projectStatus)
    {
        return view('settings.project-statuses.edit', compact('projectStatus'));
    }

    public function update(Request $request, ProjectStatus $projectStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        $projectStatus->update($validated);

        return redirect()
            ->route('project-statuses.index')
            ->with('success', 'Project Status berhasil diupdate.');
    }

    public function destroy(ProjectStatus $projectStatus)
    {
        $projectStatus->delete();
        return redirect()
            ->route('project-statuses.index')
            ->with('success', 'Project Status berhasil dihapus.');
    }
}