<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Company;
use App\Models\Project;

class TrashController extends Controller
{
    /**
     * Tampilkan halaman trash gabungan: Customer, Company, Project
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
     * Cascade restore ke projects + contacts ditangani CustomerObserver.
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
     * Cascade restore ke documents, tasks, supports ditangani ProjectObserver.
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