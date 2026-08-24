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
        $userId = auth()->id();

        $customers = Customer::onlyTrashed()->where('deleted_by', $userId)->latest('deleted_at')->get();
        $projects = Project::onlyTrashed()->where('deleted_by', $userId)->latest('deleted_at')->get();

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
        $customer = Customer::onlyTrashed()->where('deleted_by', auth()->id())->findOrFail($id);
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
        $project = Project::onlyTrashed()->where('deleted_by', auth()->id())->findOrFail($id);
        $project->restore();

        return redirect()
            ->route('trash.index')
            ->with('success', 'Project "'.$project->project_name.'" berhasil direstore');
    }

    /**
     * Hapus permanen Customer dari trash.
     */
    public function destroyCustomer(int $id)
    {
        $customer = Customer::onlyTrashed()->where('deleted_by', auth()->id())->findOrFail($id);
        $customer->forceDelete();

        return redirect()
            ->route('trash.index')
            ->with('success', 'Customer "'.$customer->name.'" dihapus permanen');
    }

    /**
     * Hapus permanen semua isi trash (Customer & Project).
     */
    public function clear()
    {
        $userId = auth()->id();

        Customer::onlyTrashed()->where('deleted_by', $userId)->forceDelete();
        Project::onlyTrashed()->where('deleted_by', $userId)->forceDelete();

        return redirect()
            ->route('trash.index')
            ->with('success', 'Trash berhasil dibersihkan');
    }
}