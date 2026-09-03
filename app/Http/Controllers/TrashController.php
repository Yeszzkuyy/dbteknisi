<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /**
     * User biasa: hanya data yang dihapus oleh dirinya sendiri (query-level).
     * Super Admin: seluruh trash, dengan info penghapus + filter per user.
     */
    public function index(Request $request)
    {
        $isSuperAdmin = auth()->user()->hasRole('super-admin');

        [$customers, $projects] = $isSuperAdmin
            ? $this->allTrash($request)
            : $this->ownTrash();

        $users = $isSuperAdmin
            ? User::whereIn('id', Customer::onlyTrashed()->pluck('deleted_by')
                ->merge(Project::onlyTrashed()->pluck('deleted_by'))
                ->filter()->unique())->orderBy('name')->get(['id', 'name'])
            : null;

        return view('trash.index', compact('customers', 'projects', 'users', 'isSuperAdmin'));
    }

    private function ownTrash(): array
    {
        $userId = auth()->id();

        return [
            Customer::onlyTrashed()->where('deleted_by', $userId)->latest('deleted_at')->get(),
            Project::onlyTrashed()->where('deleted_by', $userId)->latest('deleted_at')->get(),
        ];
    }

    private function allTrash(Request $request): array
    {
        $customerQuery = Customer::onlyTrashed()->with('deleter')->latest('deleted_at');
        $projectQuery = Project::onlyTrashed()->with('deleter')->latest('deleted_at');

        if ($request->filled('user')) {
            $customerQuery->where('deleted_by', $request->integer('user'));
            $projectQuery->where('deleted_by', $request->integer('user'));
        }

        return [$customerQuery->get(), $projectQuery->get()];
    }

    public function restoreCustomer(int $id)
    {
        $customer = $this->findTrash(Customer::class, $id);
        $customer->restore();

        return redirect()->route('trash.index')
            ->with('success', 'Customer "' . $customer->name . '" berhasil direstore');
    }

    public function restoreProject(int $id)
    {
        $project = $this->findTrash(Project::class, $id);
        $project->restore();

        return redirect()->route('trash.index')
            ->with('success', 'Project "' . $project->project_name . '" berhasil direstore');
    }

    public function destroyCustomer(int $id)
    {
        $customer = $this->findTrash(Customer::class, $id);
        $customer->forceDelete();

        return redirect()->route('trash.index')
            ->with('success', 'Customer "' . $customer->name . '" dihapus permanen');
    }

    public function destroyProject(int $id)
    {
        $project = $this->findTrash(Project::class, $id);
        $project->forceDelete();

        return redirect()->route('trash.index')
            ->with('success', 'Project "' . $project->project_name . '" dihapus permanen');
    }

    /**
     * User biasa: hanya miliknya. Super Admin: seluruh trash.
     */
    public function clear()
    {
        if (auth()->user()->hasRole('super-admin')) {
            Customer::onlyTrashed()->forceDelete();
            Project::onlyTrashed()->forceDelete();
        } else {
            $userId = auth()->id();
            Customer::onlyTrashed()->where('deleted_by', $userId)->forceDelete();
            Project::onlyTrashed()->where('deleted_by', $userId)->forceDelete();
        }

        return redirect()->route('trash.index')
            ->with('success', 'Trash berhasil dibersihkan');
    }

    /**
     * Cari data di trash dengan pembatasan kepemilikan.
     * Data milik orang lain = 404.
     */
    private function findTrash(string $model, int $id)
    {
        $query = $model::onlyTrashed()->whereKey($id);

        if (!auth()->user()->hasRole('super-admin')) {
            $query->where('deleted_by', auth()->id());
        }

        return $query->firstOrFail();
    }
}
