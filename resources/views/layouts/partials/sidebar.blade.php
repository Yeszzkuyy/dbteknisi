@php
    $navLink = 'group relative flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400/50';
    $subNavLink = 'group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400/50';
    $navActive = 'bg-accent-500/15 text-white';
    $navInactive = 'text-slate-300 hover:bg-white/5 hover:text-white';

    $dashboardActive = request()->routeIs('dashboard*');
    $customerActive = request()->routeIs('customers*');
    $managementActive = request()->routeIs('manage-sales*') || request()->routeIs('manage.*');
    $technicianActive = request()->routeIs('projects*') || request()->routeIs('teknisi.*');
    $marketingActive = request()->routeIs(['leads*', 'partners*', 'marketing.dashboard', 'whatsapp-center*']);
    $salesActive = request()->routeIs('sales.*') || request()->routeIs('projects*');
    $adminActive = request()->routeIs('admin.invoices.*') || request()->routeIs('admin.pos.*') || request()->routeIs('admin.payments.*');
    $adminPanelActive = request()->routeIs('admin-panel*');

    $roleName = auth()->user()->roles->first()?->name ?? 'User';

    // Geometri pohon branched (port BranchedMenu React Bits, px).
    $bmTrunk = 14; $bmIndent = 40; $bmRowH = 36; $bmPad = 6; $bmR = 10; $bmEndX = $bmIndent - 8;
    $bmRowY = fn($k) => $bmPad + $k * $bmRowH + $bmRowH / 2;
    $bmBranch = fn($k) => 'M '.$bmTrunk.' '.($bmRowY($k) - $bmR).' A '.$bmR.' '.$bmR.' 0 0 0 '.($bmTrunk + $bmR).' '.$bmRowY($k).' H '.$bmEndX;
    $bmReach = fn($k) => 'M '.$bmTrunk.' 0 V '.($bmRowY($k) - $bmR).' A '.$bmR.' '.$bmR.' 0 0 0 '.($bmTrunk + $bmR).' '.$bmRowY($k).' H '.$bmEndX;
    $bmLen = fn($k) => round(($bmRowY($k) - $bmR) + (M_PI * $bmR / 2) + ($bmEndX - $bmTrunk - $bmR), 1);

    // Indeks anak aktif per grup (-1 = tidak ada) untuk garis reach accent.
    $managementIdx = request()->routeIs('manage-sales.activity-log') ? 4 : (request()->routeIs('manage-sales*') ? 0 : (request()->routeIs('manage.marketing*') ? 1 : (request()->routeIs('manage.technical*') ? 2 : (request()->routeIs('manage.admin*') ? 3 : -1))));
    $teknisiIdx = request()->routeIs('teknisi.dashboard*') ? 0 : (request()->routeIs('projects*') ? 1 : (request()->routeIs('teknisi.jadwal*') ? 2 : (request()->routeIs('teknisi.surveys*') ? 3 : (request()->routeIs('teknisi.sizing-projects*') ? 4 : (request()->routeIs('teknisi.request-hargas*') ? 5 : (request()->routeIs('teknisi.instalasis*') ? 6 : (request()->routeIs('teknisi.documents*') ? 7 : -1)))))));
    $marketingIdx = request()->routeIs('marketing.dashboard') ? 0 : (request()->routeIs('whatsapp-center*') ? 1 : (request()->routeIs(['leads.index', 'leads.show', 'leads.edit']) ? 2 : (request()->routeIs('leads.pipeline') ? 3 : (request()->routeIs('partners*') ? 4 : (request()->routeIs('leads.activities') ? 5 : (request()->routeIs('leads.monitoring') ? 6 : -1))))));
    $salesIdx = request()->routeIs('sales.my-leads') ? 0 : (request()->routeIs('sales.meetings.*') ? 1 : (request()->routeIs('sales.follow-ups.*') ? 2 : (request()->routeIs('projects*') ? 3 : -1)));
    $adminIdx = request()->routeIs('admin.invoices.*') ? 0 : (request()->routeIs('admin.pos.*') ? 1 : (request()->routeIs('admin.payments.*') ? 2 : -1));
    $adminPanelIdx = request()->routeIs('admin-panel.index') ? 0 : (request()->routeIs('admin-panel.account-managers.*') ? 1 : (request()->routeIs('admin-panel.work-types.*') ? 2 : (request()->routeIs('admin-panel.document-categories.*') ? 3 : (request()->routeIs('admin-panel.project-statuses.*') ? 4 : (request()->routeIs('admin-panel.audit-log') ? 5 : -1)))));
    $hasMarketingMonitoring = auth()->user()->can('monitor-marketing');
    $hasSalesProject = auth()->user()->can('view-teknisi') || auth()->user()->can('view-sales');
@endphp

<aside class="relative flex h-full w-full flex-col overflow-hidden">
    {{-- Aurora mesh background (dekoratif, di belakang konten) --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-accent-500/15 blur-3xl"></div>
        <div class="absolute -left-24 top-1/3 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="absolute -bottom-24 right-0 h-56 w-56 rounded-full bg-cyan-400/10 blur-3xl"></div>
    </div>

    {{-- Logo --}}
    <div class="sidebar-logo relative z-10 flex h-16 sm:h-20 flex-shrink-0 items-center border-b border-white/10 px-4">
        <a wire:navigate.hover href="{{ route('dashboard') }}" class="group flex items-center gap-3">
            <picture class="shrink-0 dark:hidden">
                <source srcset="{{ asset('images/logo/logo-lightmode-256.webp') }}" type="image/webp">
                <img src="{{ asset('images/logo/logo-lightmode.png') }}" alt="Tridaya App" width="256" height="179"
                     fetchpriority="high" decoding="async"
                     class="h-9 sm:h-11 w-auto aspect-[256/179] object-contain transition-transform duration-300 group-hover:scale-[1.03]">
            </picture>
            <picture class="hidden shrink-0 dark:block">
                <source srcset="{{ asset('images/logo/logo-256.webp') }}" type="image/webp">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Tridaya App" width="256" height="181"
                     fetchpriority="high" decoding="async"
                     class="h-9 sm:h-11 w-auto aspect-[256/181] object-contain transition-transform duration-300 group-hover:scale-[1.03]">
            </picture>
            <div class="sidebar-hide min-w-0">
                <h1 class="truncate font-display text-4xl font-bold leading-none text-accent-300">3DY App</h1>
            </div>
        </a>
    </div>

    {{-- Menu --}}
    <nav id="sidebar-navigation" aria-label="{{ __('Navigasi utama') }}" class="sidebar-nav relative z-10 flex-1 overflow-y-auto px-3 py-4">
        <div class="space-y-6">
            <section aria-labelledby="sidebar-main-label">
                <p id="sidebar-main-label" class="sidebar-hide px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Main</p>
                <div class="space-y-1">
                    <a wire:navigate.hover href="{{ route('dashboard') }}"
                       aria-current="{{ $dashboardActive ? 'page' : 'false' }}"
                       class="{{ $navLink }} {{ $dashboardActive ? $navActive : $navInactive }}">
                        <x-icon name="grid" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                        <span>Dashboard</span>
                    </a>
                    <a wire:navigate.hover href="{{ route('customers.index') }}"
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
                    <p id="sidebar-departments-label" class="sidebar-hide px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Departments</p>
                    <div class="space-y-1">
                        {{-- Management (Management Hub) --}}
                        @can('manage-sales-leads')
                            <div x-data="{ open: {{ $managementActive ? 'true' : 'false' }} }" class="branched"{{ $managementActive ? 'data-open' : '' }} :data-open="open ? '' : null">
                                <a wire:navigate.hover href="{{ route('manage-sales.index') }}"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-management-menu"
                                        data-sidebar-active="{{ $managementActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} w-full {{ $managementActive ? $navActive : $navInactive }}">
                                    <x-icon name="briefcase" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Management</span>
                                    <span class="ml-auto flex items-center gap-1.5">
                                        <template x-if="$store.notif.unassigned > 0">
                                            <span class="h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-800" title="{{ __('Ada lead belum di-assign') }}"></span>
                                        </template>
                                        <svg class="h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </span>
                                </a>
                                <div id="sidebar-management-menu" class="branched-body sidebar-hide mt-1">
                                    <div class="branched-fold">
                                        <div class="branched-tree" style="height: 192px">
                                            <svg class="branched-lines" width="40" height="192" aria-hidden="true">
                                                <path class="branched-base" d="M 14 0 V 158" />
                                                @for ($k = 0; $k < 5; $k++)
                                                    <path class="branched-base" d="{{ $bmBranch($k) }}" />
                                                    <path class="branched-reach" d="{{ $bmReach($k) }}" {{ $k === $managementIdx ? 'data-bm-draw' : '' }} style="stroke-dasharray: {{ $bmLen($k) }}; stroke-dashoffset: {{ $k === $managementIdx ? 0 : $bmLen($k) }}" />
                                                @endfor
                                            </svg>
                                    <a wire:navigate.hover href="{{ route('manage-sales.index') }}"
                                       aria-current="{{ request()->routeIs('manage-sales*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage-sales*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-green-400" aria-hidden="true"></span>
                                        <span>Manage Sales</span>
                                        <template x-if="$store.notif.unassigned > 0">
                                            <span class="ml-auto h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-800" title="{{ __('Ada lead belum di-assign') }}"></span>
                                        </template>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('manage.marketing.index') }}"
                                       aria-current="{{ request()->routeIs('manage.marketing*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage.marketing*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-amber-400" aria-hidden="true"></span>
                                        <span>Manage Marketing</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('manage.technical.index') }}"
                                       aria-current="{{ request()->routeIs('manage.technical*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage.technical*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400" aria-hidden="true"></span>
                                        <span>Manage Technical</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('manage.admin.index') }}"
                                       aria-current="{{ request()->routeIs('manage.admin*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage.admin*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                        <span>Manage Admin</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('manage-sales.activity-log') }}"
                                       aria-current="{{ request()->routeIs('manage-sales.activity-log') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('manage-sales.activity-log') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-orange-400" aria-hidden="true"></span>
                                        <span>Activity Log</span>
                                    </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        {{-- Teknisi --}}
                        @can('view-teknisi')
                            <div x-data="{ open: {{ $technicianActive ? 'true' : 'false' }} }" class="branched"{{ $technicianActive ? 'data-open' : '' }} :data-open="open ? '' : null">
                                <a wire:navigate.hover href="{{ route('teknisi.dashboard') }}"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-technician-menu"
                                        data-sidebar-active="{{ $technicianActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $technicianActive ? $navActive : $navInactive }}">
                                    <x-icon name="tools" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>{{ __('Teknisi') }}</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </a>
                                <div id="sidebar-technician-menu" class="branched-body sidebar-hide mt-1">
                                    <div class="branched-fold">
                                        <div class="branched-tree" style="height: 300px">
                                            <svg class="branched-lines" width="40" height="300" aria-hidden="true">
                                                <path class="branched-base" d="M 14 0 V 266" />
                                                @for ($k = 0; $k < 8; $k++)
                                                    <path class="branched-base" d="{{ $bmBranch($k) }}" />
                                                    <path class="branched-reach" d="{{ $bmReach($k) }}" {{ $k === $teknisiIdx ? 'data-bm-draw' : '' }} style="stroke-dasharray: {{ $bmLen($k) }}; stroke-dashoffset: {{ $k === $teknisiIdx ? 0 : $bmLen($k) }}" />
                                                @endfor
                                            </svg>
                                    <a href="{{ route('teknisi.dashboard') }}"
                                       aria-current="{{ request()->routeIs('teknisi.dashboard*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.dashboard*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400" aria-hidden="true"></span>
                                        <span>{{ __('Dashboard Teknisi') }}</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('projects.index') }}"
                                       aria-current="{{ request()->routeIs('projects*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('projects*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400" aria-hidden="true"></span>
                                        <span>Project</span>
                                    </a>
                                    <a href="{{ route('teknisi.jadwal') }}"
                                       aria-current="{{ request()->routeIs('teknisi.jadwal*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.jadwal*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                        <span>{{ __('Jadwal') }}</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('teknisi.surveys.index') }}"
                                       aria-current="{{ request()->routeIs('teknisi.surveys*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.surveys*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-yellow-400" aria-hidden="true"></span>
                                        <span>Survey</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('teknisi.sizing-projects.index') }}"
                                       aria-current="{{ request()->routeIs('teknisi.sizing-projects*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.sizing-projects*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-orange-400" aria-hidden="true"></span>
                                        <span>Sizing Project</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('teknisi.request-hargas.index') }}"
                                       aria-current="{{ request()->routeIs('teknisi.request-hargas*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.request-hargas*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-red-400" aria-hidden="true"></span>
                                        <span>{{ __('Request Harga') }}</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('teknisi.instalasis.index') }}"
                                       aria-current="{{ request()->routeIs('teknisi.instalasis*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.instalasis*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                        <span>{{ __('Instalasi') }}</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('teknisi.documents.index') }}"
                                       aria-current="{{ request()->routeIs('teknisi.documents*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('teknisi.documents*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-slate-400" aria-hidden="true"></span>
                                        <span>Document</span>
                                    </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        {{-- Marketing --}}
                        @can('view-marketing')
                            <div x-data="{ open: {{ $marketingActive ? 'true' : 'false' }} }" class="branched"{{ $marketingActive ? 'data-open' : '' }} :data-open="open ? '' : null">
                                <a wire:navigate.hover href="{{ route('marketing.dashboard') }}"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-marketing-menu"
                                        data-sidebar-active="{{ $marketingActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $marketingActive ? $navActive : $navInactive }}">
                                    <x-icon name="chart-bar" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Marketing</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </a>
                                @php($mktCount = 6 + ($hasMarketingMonitoring ? 1 : 0))
                                <div id="sidebar-marketing-menu" class="branched-body sidebar-hide mt-1">
                                    <div class="branched-fold">
                                        <div class="branched-tree" style="height: {{ 12 + $mktCount * 36 }}px">
                                            <svg class="branched-lines" width="40" height="{{ 12 + $mktCount * 36 }}" aria-hidden="true">
                                                <path class="branched-base" d="M 14 0 V {{ 24 + 36 * ($mktCount - 1) - 10 }}" />
                                                @for ($k = 0; $k < $mktCount; $k++)
                                                    <path class="branched-base" d="{{ $bmBranch($k) }}" />
                                                    <path class="branched-reach" d="{{ $bmReach($k) }}" {{ $k === $marketingIdx ? 'data-bm-draw' : '' }} style="stroke-dasharray: {{ $bmLen($k) }}; stroke-dashoffset: {{ $k === $marketingIdx ? 0 : $bmLen($k) }}" />
                                                @endfor
                                            </svg>
                                    <a href="{{ route('marketing.dashboard') }}"
                                       aria-current="{{ request()->routeIs('marketing.dashboard') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('marketing.dashboard') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                        <span>Dashboard</span>
                                    </a>
                                    <a href="{{ route('whatsapp-center.index') }}"
                                       aria-current="{{ request()->routeIs('whatsapp-center*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('whatsapp-center*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-green-400" aria-hidden="true"></span>
                                        <span>WhatsApp Center</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('leads.index') }}"
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
                                    <a wire:navigate.hover href="{{ route('partners.index') }}"
                                       aria-current="{{ request()->routeIs('partners*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('partners*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-teal-400" aria-hidden="true"></span>
                                        <span>{{ __('Data Partner') }}</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('leads.activities') }}"
                                       aria-current="{{ request()->routeIs('leads.activities') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('leads.activities') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-rose-400" aria-hidden="true"></span>
                                        <span>{{ __('Log Aktivitas') }}</span>
                                    </a>
                                    @can('monitor-marketing')
                                        <a wire:navigate.hover href="{{ route('leads.monitoring') }}"
                                           aria-current="{{ request()->routeIs('leads.monitoring') ? 'page' : 'false' }}"
                                           class="{{ $subNavLink }} {{ request()->routeIs('leads.monitoring') ? $navActive : $navInactive }}">
                                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                            <span>Monitoring</span>
                                        </a>
                                    @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        {{-- Sales --}}
                        @can('view-sales')
                            <div x-data="{ open: {{ $salesActive ? 'true' : 'false' }} }" class="branched"{{ $salesActive ? 'data-open' : '' }} :data-open="open ? '' : null">
                                <a wire:navigate.hover href="{{ route('sales.my-leads') }}"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-sales-menu"
                                        data-sidebar-active="{{ $salesActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $salesActive ? $navActive : $navInactive }}">
                                    <x-icon name="calendar" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Sales</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </a>
                                @php($salesCount = 3 + ($hasSalesProject ? 1 : 0))
                                <div id="sidebar-sales-menu" class="branched-body sidebar-hide mt-1">
                                    <div class="branched-fold">
                                        <div class="branched-tree" style="height: {{ 12 + $salesCount * 36 }}px">
                                            <svg class="branched-lines" width="40" height="{{ 12 + $salesCount * 36 }}" aria-hidden="true">
                                                <path class="branched-base" d="M 14 0 V {{ 24 + 36 * ($salesCount - 1) - 10 }}" />
                                                @for ($k = 0; $k < $salesCount; $k++)
                                                    <path class="branched-base" d="{{ $bmBranch($k) }}" />
                                                    <path class="branched-reach" d="{{ $bmReach($k) }}" {{ $k === $salesIdx ? 'data-bm-draw' : '' }} style="stroke-dasharray: {{ $bmLen($k) }}; stroke-dashoffset: {{ $k === $salesIdx ? 0 : $bmLen($k) }}" />
                                                @endfor
                                            </svg>
                                    <a wire:navigate.hover href="{{ route('sales.my-leads') }}"
                                       aria-current="{{ request()->routeIs('sales.my-leads') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('sales.my-leads') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-amber-400" aria-hidden="true"></span>
                                        <span>My Leads</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('sales.meetings.index') }}"
                                       aria-current="{{ request()->routeIs('sales.meetings.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('sales.meetings.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400" aria-hidden="true"></span>
                                        <span>Tracker Meeting</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('sales.follow-ups.index') }}"
                                       aria-current="{{ request()->routeIs('sales.follow-ups.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('sales.follow-ups.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-green-400" aria-hidden="true"></span>
                                        <span>Follow Up</span>
                                    </a>
                                    @if(auth()->user()->can('view-teknisi') || auth()->user()->can('view-sales'))
                                        <a wire:navigate.hover href="{{ route('projects.index') }}"
                                           aria-current="{{ request()->routeIs('projects*') ? 'page' : 'false' }}"
                                           class="{{ $subNavLink }} {{ request()->routeIs('projects*') ? $navActive : $navInactive }}">
                                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400" aria-hidden="true"></span>
                                            <span>Project</span>
                                        </a>
                                    @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        {{-- Admin: Invoice, PO, Payment --}}
                        @can('view-admin')
                            <div x-data="{ open: {{ $adminActive ? 'true' : 'false' }} }" class="branched"{{ $adminActive ? 'data-open' : '' }} :data-open="open ? '' : null">
                                <a wire:navigate.hover href="{{ route('admin.invoices.index') }}"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-admin-menu"
                                        data-sidebar-active="{{ $adminActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $adminActive ? $navActive : $navInactive }}">
                                    <x-icon name="folder" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Admin</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </a>
                                <div id="sidebar-admin-menu" class="branched-body sidebar-hide mt-1">
                                    <div class="branched-fold">
                                        <div class="branched-tree" style="height: 120px">
                                            <svg class="branched-lines" width="40" height="120" aria-hidden="true">
                                                <path class="branched-base" d="M 14 0 V 86" />
                                                @for ($k = 0; $k < 3; $k++)
                                                    <path class="branched-base" d="{{ $bmBranch($k) }}" />
                                                    <path class="branched-reach" d="{{ $bmReach($k) }}" {{ $k === $adminIdx ? 'data-bm-draw' : '' }} style="stroke-dasharray: {{ $bmLen($k) }}; stroke-dashoffset: {{ $k === $adminIdx ? 0 : $bmLen($k) }}" />
                                                @endfor
                                            </svg>
                                    <a wire:navigate.hover href="{{ route('admin.invoices.index') }}"
                                       aria-current="{{ request()->routeIs('admin.invoices.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin.invoices.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Invoice</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('admin.pos.index') }}"
                                       aria-current="{{ request()->routeIs('admin.pos.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin.pos.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-orange-400" aria-hidden="true"></span>
                                        <span>Purchase Order</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('admin.payments.index') }}"
                                       aria-current="{{ request()->routeIs('admin.payments.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin.payments.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-green-400" aria-hidden="true"></span>
                                        <span>Payment</span>
                                    </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                </section>
            @endcanany

            @canany(['view-trash', 'manage-monitoring'])
                <section aria-labelledby="sidebar-system-label">
                    <p id="sidebar-system-label" class="sidebar-hide px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">System</p>
                    <div class="space-y-1">
                        {{-- Trash --}}
                        @can('view-trash')
                            <a wire:navigate.hover href="{{ route('trash.index') }}"
                               aria-current="{{ request()->routeIs('trash*') ? 'page' : 'false' }}"
                               class="{{ $navLink }} {{ request()->routeIs('trash*') ? $navActive : $navInactive }}">
                                <x-icon name="trash" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                <span>Trash</span>
                            </a>
                        @endcan

                        {{-- Knowledge Base (Admin only) --}}
                        @can('manage-admin')
                            <a wire:navigate.hover href="{{ route('knowledge-base.index') }}"
                               aria-current="{{ request()->routeIs('knowledge-base*') ? 'page' : 'false' }}"
                               class="{{ $navLink }} {{ request()->routeIs('knowledge-base*') ? $navActive : $navInactive }}">
                                <x-icon name="book" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                <span>Knowledge Base</span>
                            </a>
                        @endcan

                        {{-- Admin Panel (Super Admin only) --}}
                        @can('manage-monitoring')
                            <div x-data="{ open: {{ $adminPanelActive ? 'true' : 'false' }} }" class="branched"{{ $adminPanelActive ? 'data-open' : '' }} :data-open="open ? '' : null">
                                <a wire:navigate.hover href="{{ route('admin-panel.index') }}"
                                        :aria-expanded="open"
                                        aria-controls="sidebar-admin-panel-menu"
                                        data-sidebar-active="{{ $adminPanelActive ? 'true' : 'false' }}"
                                        class="{{ $navLink }} group w-full {{ $adminPanelActive ? $navActive : $navInactive }}">
                                    <x-icon name="settings" class="h-5 w-5 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                                    <span>Admin Panel</span>
                                    <svg class="ml-auto h-4 w-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </a>
                                <div id="sidebar-admin-panel-menu" class="branched-body sidebar-hide mt-1">
                                    <div class="branched-fold">
                                        <div class="branched-tree" style="height: 228px">
                                            <svg class="branched-lines" width="40" height="228" aria-hidden="true">
                                                <path class="branched-base" d="M 14 0 V 194" />
                                                @for ($k = 0; $k < 6; $k++)
                                                    <path class="branched-base" d="{{ $bmBranch($k) }}" />
                                                    <path class="branched-reach" d="{{ $bmReach($k) }}" {{ $k === $adminPanelIdx ? 'data-bm-draw' : '' }} style="stroke-dasharray: {{ $bmLen($k) }}; stroke-dashoffset: {{ $k === $adminPanelIdx ? 0 : $bmLen($k) }}" />
                                                @endfor
                                            </svg>
                                    <a wire:navigate.hover href="{{ route('admin-panel.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.index') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.index') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400" aria-hidden="true"></span>
                                        <span>User Management</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('admin-panel.account-managers.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.account-managers.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.account-managers.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Account Manager</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('admin-panel.work-types.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.work-types.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.work-types.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Work Type</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('admin-panel.document-categories.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.document-categories.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.document-categories.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Document Category</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('admin-panel.project-statuses.index') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.project-statuses.*') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.project-statuses.*') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Project Status</span>
                                    </a>
                                    <a wire:navigate.hover href="{{ route('admin-panel.audit-log') }}"
                                       aria-current="{{ request()->routeIs('admin-panel.audit-log') ? 'page' : 'false' }}"
                                       class="{{ $subNavLink }} {{ request()->routeIs('admin-panel.audit-log') ? $navActive : $navInactive }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" aria-hidden="true"></span>
                                        <span>Audit Log</span>
                                    </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                </section>
            @endcanany
        </div>
    </nav>

    {{-- User widget --}}
    <div class="relative z-10 flex-shrink-0 border-t border-white/10 p-3">
        <a wire:navigate.hover href="{{ route('profile.edit') }}"
           class="sidebar-user group flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-3 transition-all duration-300 hover:border-accent-400/30 hover:bg-white/10">
            <x-user-avatar :user="auth()->user()" size="w-9 h-9" text="text-xs" :clickable="false" />
            <span class="sidebar-hide min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold text-slate-200">{{ auth()->user()->name }}</span>
                <span class="mt-0.5 block truncate text-[11px] text-slate-400">{{ \Illuminate\Support\Str::headline($roleName) }}</span>
            </span>
            <x-icon name="chevron-right" class="sidebar-hide h-4 w-4 shrink-0 text-slate-400 transition-transform duration-300 group-hover:translate-x-1 group-hover:text-accent-300" />
        </a>
    </div>

    {{-- Toggle collapse / expand (posisi bawah, dulu logout) --}}
    <div class="relative z-10 flex-shrink-0 px-3 pb-3">
        <button id="sidebarCollapseBtn" type="button"
                class="sidebar-collapse group flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-300 transition-all duration-300 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400/50"
                aria-label="{{ __('Perkecil sidebar') }}" aria-controls="sidebar" aria-expanded="true">
            <svg class="icon-collapse h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5 5-5M18 17l-5-5 5-5"/>
            </svg>
            <svg class="icon-expand h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h10M4 12h10M4 17h10M13 12h7M17 9l3 3-3 3"/>
            </svg>
            <span class="sidebar-hide">{{ __('Perkecil') }}</span>
        </button>
    </div>
</aside>
