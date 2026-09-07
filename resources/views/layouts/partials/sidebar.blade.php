@php
    $navLink = 'group relative flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 focus-visible:ring-offset-1 dark:focus-visible:ring-offset-slate-800';
    $subNavLink = 'group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 focus-visible:ring-offset-1 dark:focus-visible:ring-offset-slate-800';
    $navActive = 'bg-blue-500/10 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300';
    $navInactive = 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white';

    $dashboardActive = request()->routeIs('dashboard*');
    $customerActive = request()->routeIs('customers*');
    $managementActive = request()->routeIs('manage-sales*') || request()->routeIs('manage.*');
    $technicianActive = request()->routeIs('projects*') || request()->routeIs('teknisi.*');
    $marketingActive = request()->routeIs(['leads*', 'partners*', 'marketing.dashboard']);
    $salesActive = request()->routeIs('sales.*') || request()->routeIs('projects*');
    $adminActive = request()->routeIs('admin.invoices.*') || request()->routeIs('admin.pos.*') || request()->routeIs('admin.payments.*');
    $adminPanelActive = request()->routeIs('admin-panel*');

    $roleName = auth()->user()->roles->first()?->name ?? 'User';
@endphp

<aside class="flex h-full w-full flex-col bg-[var(--sidebar-bg)]">
    {{-- Logo --}}
    <div class="flex-shrink-0 border-b border-[var(--sidebar-border)] p-4">
        <a href="{{ route('dashboard') }}" class="group flex items-center gap-3">
            <img src="{{ asset('images/logo/logo-lightmode.png') }}" alt="Tridaya App"
                 class="h-10 w-auto rounded-lg bg-white object-contain p-0.5 transition-transform duration-300 group-hover:scale-[1.03] dark:hidden">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Tridaya App"
                 class="hidden h-10 w-auto rounded-lg bg-white object-contain p-0.5 transition-transform duration-300 group-hover:scale-[1.03] dark:block dark:bg-transparent">
            <div class="min-w-0">
                <h1 class="truncate text-xl font-bold text-blue-600">3DY App</h1>
                <p class="mt-0.5 text-xs text-slate-400">3DY Group</p>
            </div>
        </a>
    </div>

    {{-- Menu --}}
    <nav id="sidebar-navigation" aria-label="Navigasi utama" class="flex-1 overflow-y-auto px-3 py-4">
        <div class="space-y-6">
            <section aria-labelledby="sidebar-main-label">
                <p id="sidebar-main-label" class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Main</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       aria-current="{{ $dashboardActive ? 'page' : 'false' }}"
                       class="{{ $navLink }} {{ $dashboardActive ? $navActive : $navInactive }}">
                        <x-icon name="grid" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('customers.index') }}"
                       aria-current="{{ $customerActive ? 'page' : 'false' }}"
                       class="{{ $navLink }} {{ $customerActive ? $navActive : $navInactive }}">
                        <x-icon name="users" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                        <span>Customer</span>
                    </a>
                    <a href="{{ route('ai.assistant.index') }}"
                       aria-current="{{ request()->routeIs('ai.assistant*') ? 'page' : 'false' }}"
                       class="{{ $navLink }} {{ request()->routeIs('ai.assistant*') ? $navActive : $navInactive }}">
                        <x-icon name="message" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                        <span>AI Assistant</span>
                    </a>
                </div>
            </section>

            @canany(['manage-sales-leads', 'view-teknisi', 'view-marketing', 'view-sales', 'view-admin'])
                <section aria-labelledby="sidebar-departments-label">
                    <p id="sidebar-departments-label" class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Departments</p>
                    <div class="space-y-1">
                        {{-- Management (Management Hub) --}}
                        @can('manage-sales-leads')
                            <div x-data="{ open: {{ $managementActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-management-menu"
                                        data-sidebar-active="{{ $managementActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} w-full {{ $managementActive ? $navActive : $navInactive }}">
                                    <x-icon name="briefcase" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Management</span>
                                    <span class="ml-auto flex items-center gap-1.5">
                                        <template x-if="$store.notif.unassigned > 0">
                                            <span class="h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-800" title="Ada lead belum di-assign"></span>
                                        </template>
                                        <svg class="h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </span>
                                </button>
                                <div id="sidebar-management-menu" x-cloak x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="-translate-y-1 opacity-0"
                                     x-transition:enter-end="translate-y-0 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="translate-y-0 opacity-100"
                                     x-transition:leave-end="-translate-y-1 opacity-0"
                                     class="mt-1 ml-4 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                                    <a href="{{ route('manage-sales.index') }}"
                                       aria-current="{{ request()->routeIs('manage-sales*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage-sales*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-green-400" aria-hidden="true"></span>
                                        <span>Manage Sales</span>
                                        <template x-if="$store.notif.unassigned > 0">
                                            <span class="ml-auto h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-800" title="Ada lead belum di-assign"></span>
                                        </template>
                                    </a>
                                    <a href="{{ route('manage.marketing.index') }}"
                                       aria-current="{{ request()->routeIs('manage.marketing*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage.marketing*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-amber-400" aria-hidden="true"></span>
                                        <span>Manage Marketing</span>
                                    </a>
                                    <a href="{{ route('manage.technical.index') }}"
                                       aria-current="{{ request()->routeIs('manage.technical*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage.technical*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400" aria-hidden="true"></span>
                                        <span>Manage Technical</span>
                                    </a>
                                    <a href="{{ route('manage.admin.index') }}"
                                       aria-current="{{ request()->routeIs('manage.admin*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage.admin*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                        <span>Manage Admin</span>
                                    </a>
                                    <a href="{{ route('manage-sales.activity-log') }}"
                                       aria-current="{{ request()->routeIs('manage-sales.activity-log') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage-sales.activity-log') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-orange-400" aria-hidden="true"></span>
                                        <span>Activity Log</span>
                                    </a>
                                </div>
                            </div>
                        @endcan

                        {{-- Teknisi --}}
                        @can('view-teknisi')
                            <div x-data="{ open: {{ $technicianActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-technician-menu"
                                        data-sidebar-active="{{ $technicianActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $technicianActive ? $navActive : $navInactive }}">
                                    <x-icon name="tools" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Teknisi</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="sidebar-technician-menu" x-cloak x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="-translate-y-1 opacity-0"
                                     x-transition:enter-end="translate-y-0 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="translate-y-0 opacity-100"
                                     x-transition:leave-end="-translate-y-1 opacity-0"
                                     class="mt-1 ml-4 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                                    <a href="{{ route('teknisi.dashboard') }}"
                                       aria-current="{{ request()->routeIs('teknisi.dashboard*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.dashboard*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400" aria-hidden="true"></span>
                                        <span>Dashboard Teknisi</span>
                                    </a>
                                    <a href="{{ route('projects.index') }}"
                                       aria-current="{{ request()->routeIs('projects*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('projects*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400" aria-hidden="true"></span>
                                        <span>Project</span>
                                    </a>
                                    <a href="{{ route('teknisi.jadwal') }}"
                                       aria-current="{{ request()->routeIs('teknisi.jadwal*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.jadwal*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                        <span>Jadwal</span>
                                    </a>
                                </div>
                            </div>
                        @endcan

                        {{-- Marketing --}}
                        @can('view-marketing')
                            <div x-data="{ open: {{ $marketingActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-marketing-menu"
                                        data-sidebar-active="{{ $marketingActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $marketingActive ? $navActive : $navInactive }}">
                                    <x-icon name="chart-bar" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Marketing</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="sidebar-marketing-menu" x-cloak x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="-translate-y-1 opacity-0"
                                     x-transition:enter-end="translate-y-0 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="translate-y-0 opacity-100"
                                     x-transition:leave-end="-translate-y-1 opacity-0"
                                     class="mt-1 ml-4 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                                    <a href="{{ route('marketing.dashboard') }}"
                                       aria-current="{{ request()->routeIs('marketing.dashboard') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('marketing.dashboard') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                        <span>Dashboard</span>
                                    </a>
                                    <a href="{{ route('leads.index') }}"
                                       aria-current="{{ request()->routeIs(['leads.index', 'leads.show', 'leads.edit']) ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs(['leads.index', 'leads.show', 'leads.edit']) ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-amber-400" aria-hidden="true"></span>
                                        <span>Lead / Opportunity</span>
                                    </a>
                                    <a href="{{ route('leads.pipeline') }}"
                                       aria-current="{{ request()->routeIs('leads.pipeline') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('leads.pipeline') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-sky-400" aria-hidden="true"></span>
                                        <span>Pipeline</span>
                                    </a>
                                    <a href="{{ route('partners.index') }}"
                                       aria-current="{{ request()->routeIs('partners*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('partners*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-teal-400" aria-hidden="true"></span>
                                        <span>Data Partner</span>
                                    </a>
                                    <a href="{{ route('leads.activities') }}"
                                       aria-current="{{ request()->routeIs('leads.activities') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('leads.activities') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-rose-400" aria-hidden="true"></span>
                                        <span>Log Aktivitas</span>
                                    </a>
                                    @can('monitor-marketing')
                                        <a href="{{ route('leads.monitoring') }}"
                                           aria-current="{{ request()->routeIs('leads.monitoring') ? 'page' : 'false' }}"
                                           class="{{ $subNavLink }} {{ request()->routeIs('leads.monitoring') ? $navActive : $navInactive }}">
                                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                            <span>Monitoring</span>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endcan

                        {{-- Sales --}}
                        @can('view-sales')
                            <div x-data="{ open: {{ $salesActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-sales-menu"
                                        data-sidebar-active="{{ $salesActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $salesActive ? $navActive : $navInactive }}">
                                    <x-icon name="calendar" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Sales</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="sidebar-sales-menu" x-cloak x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="-translate-y-1 opacity-0"
                                     x-transition:enter-end="translate-y-0 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="translate-y-0 opacity-100"
                                     x-transition:leave-end="-translate-y-1 opacity-0"
                                     class="mt-1 ml-4 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                                    <a href="{{ route('sales.my-leads') }}"
                                       aria-current="{{ request()->routeIs('sales.my-leads') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('sales.my-leads') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-amber-400" aria-hidden="true"></span>
                                        <span>My Leads</span>
                                    </a>
                                    <a href="{{ route('sales.meetings.index') }}"
                                       aria-current="{{ request()->routeIs('sales.meetings.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('sales.meetings.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400" aria-hidden="true"></span>
                                        <span>Tracker Meeting</span>
                                    </a>
                                    <a href="{{ route('sales.follow-ups.index') }}"
                                       aria-current="{{ request()->routeIs('sales.follow-ups.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('sales.follow-ups.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-green-400" aria-hidden="true"></span>
                                        <span>Follow Up</span>
                                    </a>
                                    @if(auth()->user()->can('view-teknisi') || auth()->user()->can('view-sales'))
                                        <a href="{{ route('projects.index') }}"
                                           aria-current="{{ request()->routeIs('projects*') ? 'page' : 'false' }}"
                                           class="{{ $subNavLink }} {{ request()->routeIs('projects*') ? $navActive : $navInactive }}">
                                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400" aria-hidden="true"></span>
                                            <span>Project</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endcan

                        {{-- Admin: Invoice, PO, Payment --}}
                        @can('view-admin')
                            <div x-data="{ open: {{ $adminActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-admin-menu"
                                        data-sidebar-active="{{ $adminActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $adminActive ? $navActive : $navInactive }}">
                                    <x-icon name="folder" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Admin</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="sidebar-admin-menu" x-cloak x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="-translate-y-1 opacity-0"
                                     x-transition:enter-end="translate-y-0 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="translate-y-0 opacity-100"
                                     x-transition:leave-end="-translate-y-1 opacity-0"
                                     class="mt-1 ml-4 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                                    <a href="{{ route('admin.invoices.index') }}"
                                       aria-current="{{ request()->routeIs('admin.invoices.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin.invoices.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Invoice</span>
                                    </a>
                                    <a href="{{ route('admin.pos.index') }}"
                                       aria-current="{{ request()->routeIs('admin.pos.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin.pos.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-orange-400" aria-hidden="true"></span>
                                        <span>Purchase Order</span>
                                    </a>
                                    <a href="{{ route('admin.payments.index') }}"
                                       aria-current="{{ request()->routeIs('admin.payments.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin.payments.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-green-400" aria-hidden="true"></span>
                                        <span>Payment</span>
                                    </a>
                                </div>
                            </div>
                        @endcan
                    </div>
                </section>
            @endcanany

            @canany(['view-trash', 'manage-monitoring'])
                <section aria-labelledby="sidebar-system-label">
                    <p id="sidebar-system-label" class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">System</p>
                    <div class="space-y-1">
                        {{-- Trash --}}
                        @can('view-trash')
                            <a href="{{ route('trash.index') }}"
                               aria-current="{{ request()->routeIs('trash*') ? 'page' : 'false' }}"
                               class="{{ $navLink }} {{ request()->routeIs('trash*') ? $navActive : $navInactive }}">
                                <x-icon name="trash" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                <span>Trash</span>
                            </a>
                        @endcan

                        {{-- Knowledge Base (Admin only) --}}
                        @can('manage-admin')
                            <a href="{{ route('knowledge-base.index') }}"
                               aria-current="{{ request()->routeIs('knowledge-base*') ? 'page' : 'false' }}"
                               class="{{ $navLink }} {{ request()->routeIs('knowledge-base*') ? $navActive : $navInactive }}">
                                <x-icon name="book" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                <span>Knowledge Base</span>
                            </a>
                        @endcan

                        {{-- Admin Panel (Super Admin only) --}}
                        @can('manage-monitoring')
                            <div x-data="{ open: {{ $adminPanelActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-admin-panel-menu"
                                        data-sidebar-active="{{ $adminPanelActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $adminPanelActive ? $navActive : $navInactive }}">
                                    <x-icon name="settings" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Admin Panel</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="sidebar-admin-panel-menu" x-cloak x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="-translate-y-1 opacity-0"
                                     x-transition:enter-end="translate-y-0 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="translate-y-0 opacity-100"
                                     x-transition:leave-end="-translate-y-1 opacity-0"
                                     class="mt-1 ml-4 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                                    <a href="{{ route('admin-panel.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.index') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.index') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400" aria-hidden="true"></span>
                                        <span>User Management</span>
                                    </a>
                                    <a href="{{ route('admin-panel.account-managers.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.account-managers.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.account-managers.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Account Manager</span>
                                    </a>
                                    <a href="{{ route('admin-panel.work-types.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.work-types.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.work-types.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Work Type</span>
                                    </a>
                                    <a href="{{ route('admin-panel.document-categories.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.document-categories.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.document-categories.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Document Category</span>
                                    </a>
                                    <a href="{{ route('admin-panel.project-statuses.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.project-statuses.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.project-statuses.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Project Status</span>
                                    </a>
                                    <a href="{{ route('admin-panel.audit-log') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.audit-log') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.audit-log') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Audit Log</span>
                                    </a>
                                </div>
                            </div>
                        @endcan
                    </div>
                </section>
            @endcanany
        </div>
    </nav>

    {{-- User widget --}}
    <div class="flex-shrink-0 border-t border-[var(--sidebar-border)] p-3">
        <a href="{{ route('profile.edit') }}"
           class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 transition-all duration-300 hover:border-blue-200 hover:bg-blue-50/70 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:border-blue-500/30 dark:hover:bg-blue-500/10">
            <x-user-avatar :user="auth()->user()" size="w-9 h-9" text="text-xs" :clickable="false" />
            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</span>
                <span class="mt-0.5 block truncate text-[11px] text-slate-400">{{ \Illuminate\Support\Str::headline($roleName) }}</span>
            </span>
            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-300 group-hover:translate-x-1 group-hover:text-blue-500" />
        </a>
    </div>

    {{-- Logout --}}
    <div class="flex-shrink-0 px-3 pb-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="group flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-red-600 transition-all duration-300 hover:bg-red-50 hover:text-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40 focus-visible:ring-offset-1 dark:text-red-300 dark:hover:bg-red-500/10 dark:hover:text-red-200 dark:focus-visible:ring-offset-slate-800">
                <x-icon name="logout" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
