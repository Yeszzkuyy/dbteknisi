<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectDocument;
use App\Enums\ProjectStatus;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasAnyRole('manager', 'super-admin')) {
            $user = auth()->user();

            if ($user->can('view-marketing') || $user->can('manage-marketing')) {
                return redirect()->route('marketing.dashboard');
            }

            if ($user->can('view-teknisi') || $user->can('manage-teknisi')) {
                return redirect()->route('teknisi.dashboard');
            }

            // ponytail: sales & admin belum punya dashboard divisi; tambah redirect di sini saat dashboard mereka dibuat
            abort(403, 'Anda tidak memiliki akses ke dashboard umum.');
        }

        // Total Customer
        $customerCount = Customer::count();

        // Total Dokumen
        $documentCount = ProjectDocument::count();

        // Total User
        $userCount = User::count();

        // Total Project (semua)
        $totalProjects = Project::count();

        // Project Aktif (Open + Progress)
        $activeProjects = Project::whereHas('status', fn ($q) => $q->whereIn('name', [
            ProjectStatus::Open->value,
            ProjectStatus::OnProgress->value,
        ]))->count();

        // Aktivitas Terbaru
        $activities = ProjectActivity::with(['project', 'user'])
            ->latest('activity_date')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'customerCount',
            'documentCount',
            'userCount',
            'totalProjects',
            'activeProjects',
            'activities'
        ));
    }
}