<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectTask;

class TrashController extends Controller
{
    /**
     * Tampilkan halaman trash gabungan: Customer, Project
     * yang sudah di-soft-delete.
     */
    public function index()
    {
        $customers = Customer::onlyTrashed()->latest('deleted_at')->get();
        $projects = Project::onlyTrashed()->latest('deleted_at')->get();

        return view(
            'trash.index',
            compact('customers', 'projects')
        );
    }

    /**
     * Restore Customer yang sudah di-soft-delete.
     */
    public function restoreCustomer(int $id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();

        return redirect()
            ->route('trash.index')
            ->with('success', 'Customer "'.$customer->name.'" berhasil direstore');
    }

    /**
     * Restore Project yang sudah di-soft-delete.
     */
    public function restoreProject(int $id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();

        return redirect()
            ->route('trash.index')
            ->with('success', 'Project "'.$project->project_name.'" berhasil direstore');
    }
}