@php
    $currentRoute = request()->route()->getName();
@endphp

<aside class="w-full h-full bg-[var(--sidebar-bg)] flex flex-col">
    {{-- Logo --}}
    <div class="p-4 border-b border-[var(--sidebar-border)] flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo/logo-lightmode.png') }}" alt="Tridaya App"
                 class="h-10 w-auto object-contain bg-white rounded-lg p-0.5 dark:bg-transparent dark:hidden">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Tridaya App"
                 class="h-10 w-auto object-contain bg-white rounded-lg p-0.5 hidden dark:block dark:bg-transparent">
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-blue-600 truncate">3DY App</h1>
                <p class="text-xs text-slate-400 mt-0.5">3DY Group</p>
            </div>
        </a>
    </div>

    {{-- Menu --}}
    <nav class="p-3 space-y-1 flex-1 overflow-y-auto">
        <!-- Dashboard (Manager & Super Admin saja) -->
        @role('manager|super-admin')
            <a href="{{ route('dashboard') }}"
               class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                <x-icon name="grid" class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" />
                <span>Dashboard</span>
            </a>
        @endrole

        {{-- Monitoring (Manager & Super Admin) --}}
        @can('view-monitoring')
            <a href="{{ route('monitoring.index') }}"
               class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('monitoring*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Monitoring</span>
            </a>
        @endcan

        <!-- Customer -->
        <a href="{{ route('customers.index') }}"
           class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('customers*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
            <x-icon name="users" class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" />
            <span>Customer</span>
        </a>

        {{-- Teknisi --}}
        @can('view-teknisi')
            <div x-data="{ open: {{ request()->routeIs('projects*') || request()->routeIs('teknisi.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('projects*') || request()->routeIs('teknisi.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <x-icon name="tools" class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" />
                    <span>Teknisi</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('teknisi.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('teknisi.dashboard*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        Dashboard Teknisi
                    </a>
                    <a href="{{ route('projects.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('projects*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                        Project
                    </a>
                    <a href="{{ route('teknisi.jadwal') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('teknisi.jadwal*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span>
                        Jadwal
                    </a>
                </div>
            </div>
        @endcan

        {{-- Marketing --}}
        @can('view-marketing')
            <div x-data="{ open: {{ request()->routeIs('leads*') || request()->routeIs('partners*') || request()->routeIs('marketing.dashboard') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs(['leads*', 'partners*', 'marketing.dashboard']) ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <x-icon name="chart-bar" class="w-5 h-5" />
                    <span>Marketing</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('marketing.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('marketing.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Dashboard
                    </a>
                    <a href="{{ route('leads.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs(['leads.index', 'leads.show', 'leads.edit']) ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        Lead / Opportunity
                    </a>
                    <a href="{{ route('leads.pipeline') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('leads.pipeline') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        Pipeline
                    </a>
                    <a href="{{ route('partners.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('partners*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                        Data Partner
                    </a>
                    <a href="{{ route('leads.activities') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('leads.activities') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                        Log Aktivitas
                    </a>
                    @can('monitor-marketing')
                        <a href="{{ route('leads.monitoring') }}"
                           class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('leads.monitoring') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                            <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span>
                            Monitoring
                        </a>
                    @endcan
                </div>
            </div>
        @endcan

        @can('view-sales')
            <div x-data="{ open: {{ request()->routeIs('sales.*') || request()->routeIs('projects*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('sales.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <x-icon name="calendar" class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" />
                    <span>Sales</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('sales.meetings.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('sales.meetings.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        Tracker Meeting
                    </a>
                    <a href="{{ route('sales.follow-ups.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('sales.follow-ups.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        Follow Up
                    </a>
                    @if(auth()->user()->can('view-teknisi') || auth()->user()->can('view-sales'))
                        <a href="{{ route('projects.index') }}"
                           class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('projects*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                            Project
                        </a>
                    @endif
                </div>
            </div>
        @endcan

        <!-- Admin: Invoice, PO, Payment -->
        @can('view-admin')
            <div x-data="{ open: {{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.pos.*') || request()->routeIs('admin.payments.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.pos.*') || request()->routeIs('admin.payments.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <x-icon name="folder" class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" />
                    <span>Admin</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('admin.invoices.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin.invoices.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        Invoice
                    </a>
                    <a href="{{ route('admin.pos.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin.pos.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                        Purchase Order
                    </a>
                    <a href="{{ route('admin.payments.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin.payments.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        Payment
                    </a>
                </div>
            </div>
        @endcan

        <!-- Trash -->
        @can('view-trash')
            <a href="{{ route('trash.index') }}"
               class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('trash*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                <x-icon name="trash" class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" />
                <span>Trash</span>
            </a>
        @endcan

        {{-- Admin Panel (Super Admin only) --}}
        @can('manage-monitoring')
            <div x-data="{ open: {{ request()->routeIs('admin-panel*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin-panel*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Admin Panel</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" class="mt-1 ml-4 space-y-1">
                    <a href="{{ route('admin-panel.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin-panel.index') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        User Management
                    </a>
                    <a href="{{ route('admin-panel.account-managers.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin-panel.account-managers.*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        Account Manager
                    </a>
                    <a href="{{ route('admin-panel.work-types.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin-panel.work-types.*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        Work Type
                    </a>
                    <a href="{{ route('admin-panel.document-categories.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin-panel.document-categories.*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        Document Category
                    </a>
                    <a href="{{ route('admin-panel.project-statuses.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin-panel.project-statuses.*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        Project Status
                    </a>
                    <a href="{{ route('admin-panel.audit-log') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm {{ request()->routeIs('admin-panel.audit-log') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-50' }} transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        Audit Log
                    </a>
                </div>
            </div>
        @endcan
    </nav>

    {{-- Logout --}}
    <div class="p-3 border-t border-[var(--sidebar-border)] flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 transition-all duration-200">
                <x-icon name="logout" class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-1" />
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>