<?php

namespace App\Http\Controllers;

use App\Models\WorkType;
use Illuminate\Http\Request;

class WorkTypeController extends Controller
{
    public function index()
    {
        $workTypes = WorkType::latest()->get();
        return view('settings.work-types.index', compact('workTypes'));
    }

    public function create()
    {
        return view('settings.work-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        WorkType::create($validated);

        return redirect()
            ->route('work-types.index')
            ->with('success', 'Work Type berhasil ditambahkan.');
    }

    public function edit(WorkType $workType)
    {
        return view('settings.work-types.edit', compact('workType'));
    }

    public function update(Request $request, WorkType $workType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $workType->update($validated);

        return redirect()
            ->route('work-types.index')
            ->with('success', 'Work Type berhasil diupdate.');
    }

    public function destroy(WorkType $workType)
    {
        $workType->delete();
        return redirect()
            ->route('work-types.index')
            ->with('success', 'Work Type berhasil dihapus.');
    }
}