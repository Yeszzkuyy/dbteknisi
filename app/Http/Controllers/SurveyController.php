<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Project;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = Survey::with('project.customer')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $surveys = $query->get();
        $projects = Project::with('customer')->orderBy('project_name')->get();

        return view('teknisi.surveys.index', compact('surveys', 'projects'));
    }

    public function create(Request $request)
    {
        $projects = Project::with('customer')->orderBy('project_name')->get();
        $selectedProject = $request->query('project_id');

        return view('teknisi.surveys.create', compact('projects', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'sales_request' => 'nullable|string',
            'survey_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'survey_data' => 'nullable|string',
            'survey_report' => 'nullable|string',
            'status' => 'required|in:draft,scheduled,on_survey,completed',
            'notes' => 'nullable|string',
        ]);

        Survey::create($validated);

        return redirect()->route('teknisi.surveys.index')
            ->with('success', 'Survey berhasil ditambahkan.');
    }

    public function show(Survey $survey)
    {
        $survey->load('project.customer');

        return view('teknisi.surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        $projects = Project::with('customer')->orderBy('project_name')->get();

        return view('teknisi.surveys.edit', compact('survey', 'projects'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'sales_request' => 'nullable|string',
            'survey_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'survey_data' => 'nullable|string',
            'survey_report' => 'nullable|string',
            'status' => 'required|in:draft,scheduled,on_survey,completed',
            'notes' => 'nullable|string',
        ]);

        $survey->update($validated);

        return redirect()->route('teknisi.surveys.show', $survey)
            ->with('success', 'Survey berhasil diupdate.');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()->route('teknisi.surveys.index')
            ->with('success', 'Survey berhasil dihapus.');
    }
}
