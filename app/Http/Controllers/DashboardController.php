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