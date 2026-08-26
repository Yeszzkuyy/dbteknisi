<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 truncate">
                    {{ $customer->name }}
                </h1>
                <p class="text-sm sm:text-base text-slate-500 mt-1 truncate">
                    Detail Customer • {{ $customer->address ?? 'Alamat tidak tersedia' }}
                </p>
            </div>
            <div class="flex gap-2 shrink-0">
                @can('manage-sales')
                    <a href="{{ route('customers.edit', $customer) }}" 
                       class="flex-1 sm:flex-none text-center px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium transition">
                        Edit Customer
                    </a>
                @endcan
                <a href="{{ route('customers.index') }}" 
                   class="flex-1 sm:flex-none text-center px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">
                    Kembali
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'overview' }" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
            
            {{-- Tab Navigation --}}
            <div class="border-b border-slate-200 dark:border-slate-600">
                <nav class="flex gap-4 sm:gap-6 px-4 sm:px-6 overflow-x-auto whitespace-nowrap scrollbar-hide -mb-px" x-ref="tabs">
                    <button @click="tab = 'overview'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'overview', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'overview' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        📋 Overview
                    </button>
                    <button @click="tab = 'projects'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'projects', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'projects' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        📁 Projects
                    </button>
                    <button @click="tab = 'contacts'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'contacts', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'contacts' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        👤 Contacts
                    </button>
                    <button @click="tab = 'documents'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'documents', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'documents' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        📄 Documents
                    </button>
                    @can('view-sales')
                    <button @click="tab = 'meetings'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'meetings', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'meetings' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        🤝 Meetings
                    </button>
                    <button @click="tab = 'followups'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'followups', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'followups' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        📞 Follow Up
                    </button>
                    @endcan
                    @can('view-admin')
                    <button @click="tab = 'invoices'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'invoices', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'invoices' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        📋 Invoice
                    </button>
                    <button @click="tab = 'pos'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'pos', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'pos' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        📑 PO
                    </button>
                    <button @click="tab = 'payments'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'payments', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'payments' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        💳 Payment
                    </button>
                    @endcan
                    <button @click="tab = 'activity'" 
                            :class="{ 'border-indigo-500 text-indigo-600': tab === 'activity', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'activity' }"
                            class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition">
                        📝 Activity
                    </button>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div class="p-6">
                
                {{-- TAB 1: OVERVIEW --}}
                <div x-show="tab === 'overview'" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Info Perusahaan --}}
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-6">
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Informasi Perusahaan</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-slate-400">Nama</p>
                                    <p class="font-medium text-slate-800 dark:text-slate-100">{{ $customer->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Alamat</p>
                                    <p class="font-medium text-slate-800 dark:text-slate-100">{{ $customer->address ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Telepon</p>
                                    <p class="font-medium text-slate-800 dark:text-slate-100">{{ $customer->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Email</p>
                                    <p class="font-medium text-slate-800 dark:text-slate-100">{{ $customer->email ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Statistik --}}
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-6">
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Statistik</h3>
                            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                <div class="bg-white rounded-lg p-3 sm:p-4 text-center">
                                    <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $customer->projects->count() }}</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500">Total Project</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-green-600">
                                        {{ $customer->projects->where('project_status_id', 5)->count() }}
                                    </p>
                                    <p class="text-xs text-slate-500">Project Selesai</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-yellow-600">
                                        {{ $customer->projects->where('project_status_id', '!=', 5)->where('project_status_id', '!=', 6)->count() }}
                                    </p>
                                    <p class="text-xs text-slate-500">Project Aktif</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-purple-600">{{ $customer->contacts->count() }}</p>
                                    <p class="text-xs text-slate-500">Total PIC</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Activity --}}
                    <div class="mt-6 bg-slate-50 dark:bg-slate-900/50 rounded-xl p-6">
                        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Aktivitas Terbaru</h3>
                        @php
                            $activities = App\Models\ProjectActivity::whereIn('project_id', $customer->projects->pluck('id'))
                                ->with(['project', 'user'])
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp
                        @forelse($activities as $activity)
                            <div class="flex items-start gap-3 py-2 border-b border-slate-200 last:border-0">
                                <x-user-avatar :user="$activity->user" size="w-8 h-8" text="text-xs" />
                                <div>
                                    <p class="text-sm text-slate-800 dark:text-slate-100">
                                        <span class="font-semibold">{{ $activity->user?->name ?? 'System' }}</span>
                                        {{ $activity->title ?? 'Activity' }}
                                        <span class="text-xs text-slate-400">· {{ $activity->created_at->diffForHumans() }}</span>
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $activity->project?->name ?? 'Project' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-sm text-center py-4">Belum ada aktivitas.</p>
                        @endforelse
                    </div>
                </div>

                {{-- TAB 2: PROJECTS --}}
                <div x-show="tab === 'projects'" x-transition>
                    <x-section-header title="Daftar Project">
                        @can('manage-teknisi')
                            <x-add-button href="{{ route('projects.create', ['customer_id' => $customer->id]) }}">
                                + Tambah Project
                            </x-add-button>
                        @endcan
                    </x-section-header>

                    @if($customer->projects->isNotEmpty())
                        <x-data-table>
                            <thead class="bg-slate-50 dark:bg-slate-700">
                                <tr>
                                    <x-th>Nama Project</x-th>
                                    <x-th>Status</x-th>
                                    <x-th>Progress</x-th>
                                    <x-th>Tanggal Dibuat</x-th>
                                    <x-th class="text-right">Aksi</x-th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                                @foreach($customer->projects as $project)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                        <td class="px-6 py-3 font-medium text-slate-800 dark:text-slate-100">{{ $project->project_name }}</td>

                                        {{-- Status --}}
                                        <td class="px-6 py-3">
                                            <x-status-badge :color="$project->status?->color ?? 'slate'">
                                                {{ $project->status?->name ?? 'Belum Memulai' }}
                                            </x-status-badge>
                                        </td>

                                        {{-- Progress --}}
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                                                    <div class="h-full bg-blue-600 rounded-full" style="width: {{ $project->progress ?? 0 }}%"></div>
                                                </div>
                                                <span class="text-xs text-slate-600 dark:text-slate-300">{{ $project->progress ?? 0 }}%</span>
                                            </div>
                                        </td>

                                        {{-- Tanggal Dibuat --}}
                                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-300">{{ $project->created_at->format('d M Y') }}</td>

                                        {{-- Aksi --}}
                                        <td class="px-6 py-3 text-right">
                                            <div class="flex flex-wrap justify-end items-center gap-x-3 gap-y-1">
                                                <a href="{{ route('projects.show', $project) }}"
                                                   class="text-blue-600 hover:text-blue-800 text-sm whitespace-nowrap">Detail</a>
                                                @can('manage-teknisi')
                                                    <a href="{{ route('projects.edit', $project) }}"
                                                       class="text-amber-600 hover:text-amber-800 text-sm whitespace-nowrap">Edit</a>
                                                @endcan
                                                @can('manage-teknisi')
                                                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                                          onsubmit="return confirm('Hapus project ini?')" class="inline-block m-0">
                                                        @csrf @method('DELETE')
                                                        <button class="text-red-600 hover:text-red-800 text-sm whitespace-nowrap">Hapus</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-data-table>
                    @else
                        <x-empty-state label="project" />
                    @endif
                </div>

                {{-- TAB 3: CONTACTS --}}
                <div x-show="tab === 'contacts'" x-transition>
                    <x-section-header title="Daftar PIC / Customer Contacts">
                        @can('manage-sales')
                            <x-add-button href="{{ route('customer-contacts.create', $customer) }}">
                                + Tambah PIC
                            </x-add-button>
                        @endcan
                    </x-section-header>

                    @if($customer->contacts->isNotEmpty())
                        <x-data-table>
                            <thead class="bg-slate-50 dark:bg-slate-700">
                                <tr>
                                    <x-th>Nama</x-th>
                                    <x-th>Jabatan</x-th>
                                    <x-th>Phone & Email</x-th>
                                    <x-th>Status</x-th>
                                    <x-th class="text-right">Aksi</x-th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                                @foreach($customer->contacts as $contact)
                                    <tr class="{{ $contact->is_primary ? 'bg-indigo-50 dark:bg-indigo-900/30' : '' }}">
                                        <td class="px-6 py-3 font-medium text-slate-800 dark:text-slate-100 align-middle">{{ $contact->name }}</td>
                                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-300 align-middle">{{ $contact->position ?? '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-300 align-middle">
                                            {{ $contact->phone ?? '-' }} <br>
                                            <span class="text-xs text-slate-400">{{ $contact->email ?? '' }}</span>
                                        </td>
                                        <td class="px-6 py-3 align-middle">
                                            @if($contact->is_primary)
                                                <x-status-badge color="green" icon="★">Primary PIC</x-status-badge>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-right align-middle">
                                            @can('manage-sales')
                                                <a href="{{ route('customer-contacts.edit', $contact) }}"
                                                   class="text-amber-600 hover:text-amber-800 text-sm inline-block align-middle">Edit</a>
                                            @endcan
                                            @can('manage-sales')
                                                <form action="{{ route('customer-contacts.destroy', $contact) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Hapus PIC ini?')"
                                                      class="inline-block align-middle ml-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800 text-sm inline-block align-middle bg-transparent border-0 cursor-pointer p-0">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-data-table>
                    @else
                        <x-empty-state label="PIC" />
                    @endif
                </div>
                
                {{-- TAB 4: DOCUMENTS --}}
                <div x-show="tab === 'documents'" x-transition>
                    @php
                        $firstProject = $customer->projects->first();
                    @endphp
                    <x-section-header title="Dokumen">
                        @can('manage-teknisi')
                            @if($firstProject)
                                <x-add-button href="{{ route('project-documents.index', $firstProject) }}">
                                    + Kelola Dokumen
                                </x-add-button>
                            @else
                                <span class="inline-flex justify-center items-center px-4 py-2 bg-gray-300 text-white text-sm rounded-md cursor-not-allowed">
                                    Belum ada project
                                </span>
                            @endif
                        @endcan
                    </x-section-header>
                    
                    @php
                        // Ambil semua project ID dari customer
                        $projectIds = $customer->projects->pluck('id')->toArray();
                        
                        // Query dokumen berdasarkan project ID
                        $documents = App\Models\ProjectDocument::whereIn('project_id', $projectIds)
                            ->with(['project', 'category', 'uploader'])
                            ->whereNull('deleted_at')
                            ->latest()
                            ->get();
                    @endphp

                    @if($documents->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($documents as $doc)
                                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-4 border border-slate-200 hover:shadow-md transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            {{-- Icon --}}
                                            <div class="flex items-center gap-2 mb-1">
                                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p class="font-medium text-slate-800 dark:text-slate-100 text-sm truncate">{{ $doc->file_name }}</p>
                                            </div>
                                            <p class="text-xs text-slate-500">{{ $doc->project?->project_name ?? 'Project' }}</p>
                                            <p class="text-xs text-slate-400">{{ $doc->category?->name ?? 'Uncategorized' }}</p>
                                            <p class="text-xs text-slate-400 mt-1">{{ number_format($doc->file_size / 1024, 1) }} KB</p>
                                            <p class="text-xs text-slate-400">Upload: {{ $doc->uploader?->name ?? '-' }}</p>
                                        </div>
                                        <div class="flex gap-2 flex-shrink-0 ml-2">
                                            <a href="{{ route('project-documents.preview', $doc) }}"
                                               target="_blank"
                                               class="text-indigo-600 hover:text-indigo-800 text-xs whitespace-nowrap">Preview</a>
                                            <a href="{{ route('project-documents.download', $doc) }}"
                                               class="text-blue-600 hover:text-blue-800 text-xs">Download</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-empty-state label="dokumen" :description="$firstProject ? null : 'Buat project terlebih dahulu untuk upload dokumen.'">
                            @if($firstProject)
                                <a href="{{ route('project-documents.index', $firstProject) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Upload dokumen sekarang
                                </a>
                            @endif
                        </x-empty-state>
                    @endif
                </div>

                {{-- TAB 5: MEETINGS --}}
                @can('view-sales')
                <div x-show="tab === 'meetings'" x-transition>
                    <x-section-header title="Daftar Meeting">
                        @can('manage-sales')
                            <x-add-button href="{{ route('sales.meetings.create', ['customer_id' => $customer->id]) }}">
                                + Catat Meeting
                            </x-add-button>
                        @endcan
                    </x-section-header>

                    @php
                        $meetings = $customer->meetings()->with('creator')->latest('meeting_date')->get();
                    @endphp
                    @if($meetings->isNotEmpty())
                        @foreach($meetings as $meeting)
                            <div class="border-b border-slate-100 py-4 last:border-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $meeting->meeting_date->format('d M Y') }}</p>
                                        <p class="text-sm text-slate-500 mt-1">Peserta: {{ $meeting->participants ?? '-' }}</p>
                                    </div>
                                    <a href="{{ route('sales.meetings.show', $meeting) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                </div>
                                @if($meeting->user_needs)
                                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-2">
                                        <span class="font-medium">Kebutuhan:</span> {{ Str::limit($meeting->user_needs, 150) }}
                                    </p>
                                @endif
                                @if($meeting->user_complaints)
                                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                        <span class="font-medium">Keluhan:</span> {{ Str::limit($meeting->user_complaints, 150) }}
                                    </p>
                                @endif
                                <p class="text-xs text-slate-400 mt-2">oleh {{ $meeting->creator?->name ?? '-' }}</p>
                            </div>
                        @endforeach
                    @else
                        <x-empty-state label="meeting" />
                    @endif
                </div>

                {{-- TAB 6: FOLLOW UPS --}}
                <div x-show="tab === 'followups'" x-transition>
                    <x-section-header title="Follow Up">
                        @can('manage-sales')
                            <x-add-button href="{{ route('sales.follow-ups.create', ['customer_id' => $customer->id]) }}">
                                + Tambah Follow Up
                            </x-add-button>
                        @endcan
                    </x-section-header>

                    @php
                        $followups = $customer->followUps()->with(['meeting', 'creator'])->latest('follow_up_date')->get();
                    @endphp
                    @if($followups->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($followups as $fu)
                                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-4 border border-slate-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm text-slate-700">{{ $fu->description }}</p>
                                            <div class="flex gap-3 mt-2 text-xs text-slate-400">
                                                <span>{{ $fu->creator?->name ?? '-' }}</span>
                                                @if($fu->follow_up_date)
                                                    <span>· {{ $fu->follow_up_date->format('d M Y') }}</span>
                                                @endif
                                                @if($fu->meeting)
                                                    <span>· Terkait Meeting {{ $fu->meeting->meeting_date->format('d M Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-empty-state label="follow up" />
                    @endif
                </div>
                @endcan

                {{-- TAB 7: INVOICES --}}
                @can('view-admin')
                <div x-show="tab === 'invoices'" x-transition>
                    <x-section-header title="Invoice">
                        @can('manage-admin')
                            <x-add-button href="{{ route('admin.invoices.create', ['customer_id' => $customer->id]) }}">
                                + Buat Invoice
                            </x-add-button>
                        @endcan
                    </x-section-header>

                    @php $custInvoices = $customer->invoices()->with('payments')->latest('issue_date')->get(); @endphp
                    @if($custInvoices->isNotEmpty())
                        <x-data-table>
                            <thead class="bg-slate-50 dark:bg-slate-700">
                                <tr>
                                    <x-th>No Invoice</x-th>
                                    <x-th class="text-right">Nominal</x-th>
                                    <x-th class="text-center">Status</x-th>
                                    <x-th class="text-right">Aksi</x-th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                                @foreach($custInvoices as $inv)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                        <td class="px-6 py-3 font-mono text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $inv->invoice_number }}</td>
                                        <td class="px-6 py-3 text-right font-mono text-slate-800 dark:text-slate-100">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-3 text-center">
                                            @php
                                                $invBadgeColor = match($inv->status) {
                                                    'paid' => 'green',
                                                    'cancelled' => 'red',
                                                    default => 'yellow',
                                                };
                                            @endphp
                                            <x-status-badge :color="$invBadgeColor">{{ $inv->status === 'paid' ? 'Lunas' : ($inv->status === 'cancelled' ? 'Dibatalkan' : 'Belum Bayar') }}</x-status-badge>
                                        </td>
                                        <td class="px-6 py-3 text-right"><a href="{{ route('admin.invoices.show', $inv) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-data-table>
                    @else
                        <x-empty-state label="invoice" />
                    @endif
                </div>

                {{-- TAB 8: PO --}}
                <div x-show="tab === 'pos'" x-transition>
                    <x-section-header title="Purchase Order">
                        @can('manage-admin')
                            <x-add-button href="{{ route('admin.pos.create', ['customer_id' => $customer->id]) }}">
                                + Buat PO
                            </x-add-button>
                        @endcan
                    </x-section-header>

                    @php $custPos = $customer->purchaseOrders()->latest('issue_date')->get(); @endphp
                    @if($custPos->isNotEmpty())
                        <x-data-table>
                            <thead class="bg-slate-50 dark:bg-slate-700">
                                <tr>
                                    <x-th>No PO</x-th>
                                    <x-th>Item</x-th>
                                    <x-th class="text-center">Status</x-th>
                                    <x-th class="text-right">Aksi</x-th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                                @foreach($custPos as $po)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                        <td class="px-6 py-3 font-mono text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $po->po_number }}</td>
                                        <td class="px-6 py-3 text-slate-600 dark:text-slate-300 max-w-xs truncate">{{ Str::limit($po->items, 60) }}</td>
                                        <td class="px-6 py-3 text-center">
                                            @php
                                                $poBadgeColor = match($po->status) {
                                                    'selesai' => 'green',
                                                    'dibatalkan' => 'red',
                                                    'diproses' => 'yellow',
                                                    default => 'slate',
                                                };
                                            @endphp
                                            <x-status-badge :color="$poBadgeColor">{{ ucfirst($po->status) }}</x-status-badge>
                                        </td>
                                        <td class="px-6 py-3 text-right"><a href="{{ route('admin.pos.show', $po) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-data-table>
                    @else
                        <x-empty-state label="PO" />
                    @endif
                </div>

                {{-- TAB 9: PAYMENTS --}}
                <div x-show="tab === 'payments'" x-transition>
                    <x-section-header title="Pembayaran">
                        @can('manage-admin')
                            <x-add-button href="{{ route('admin.payments.create') }}">
                                + Catat Pembayaran
                            </x-add-button>
                        @endcan
                    </x-section-header>

                    @php $custPayments = \App\Models\Payment::whereHas('invoice', fn($q) => $q->where('customer_id', $customer->id))->with('invoice')->latest('payment_date')->get(); @endphp
                    @if($custPayments->isNotEmpty())
                        <x-data-table>
                            <thead class="bg-slate-50 dark:bg-slate-700">
                                <tr>
                                    <x-th>Invoice</x-th>
                                    <x-th>Tgl Bayar</x-th>
                                    <x-th class="text-right">Nominal</x-th>
                                    <x-th class="text-center">Bukti</x-th>
                                    <x-th class="text-right">Aksi</x-th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                                @foreach($custPayments as $pm)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700">
                                        <td class="px-6 py-3 font-mono text-sm text-slate-800 dark:text-slate-100">{{ $pm->invoice->invoice_number }}</td>
                                        <td class="px-6 py-3 text-slate-600 dark:text-slate-300">{{ $pm->payment_date->format('d M Y') }}</td>
                                        <td class="px-6 py-3 text-right font-mono text-slate-800 dark:text-slate-100">Rp {{ number_format($pm->amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-3 text-center">@if($pm->proof_file)<a href="{{ asset('storage/' . $pm->proof_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs underline">Lihat</a>@else - @endif</td>
                                        <td class="px-6 py-3 text-right"><a href="{{ route('admin.payments.show', $pm) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-data-table>
                    @else
                        <x-empty-state label="pembayaran" />
                    @endif
                </div>
                @endcan

                {{-- TAB 10: ACTIVITY --}}
                <div x-show="tab === 'activity'" x-transition>
                    <x-section-header title="Timeline Aktivitas" />

                    @php
                        $allActivities = App\Models\ProjectActivity::whereIn('project_id', $customer->projects->pluck('id'))
                            ->with(['project', 'user'])
                            ->latest()
                            ->get();
                    @endphp

                    @if($allActivities->isNotEmpty())
                        <div class="relative">
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                            @foreach($allActivities as $activity)
                                <div class="relative pl-12 pb-6 last:pb-0">
                                    <div class="absolute left-2 top-1 w-5 h-5 rounded-full bg-indigo-500 border-4 border-white shadow-sm"></div>
                                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-4">
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold text-sm text-slate-800 dark:text-slate-100">{{ $activity->user?->name ?? 'System' }}</span>
                                            <span class="text-xs text-slate-400">{{ $activity->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-slate-700 mt-1">{{ $activity->title ?? 'Activity' }}</p>
                                        <p class="text-xs text-slate-500">{{ $activity->project?->name ?? 'Project' }}</p>
                                        @if($activity->description)
                                            <p class="text-xs text-slate-400 mt-1">{{ $activity->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-empty-state label="aktivitas" />
                    @endif
                </div>

            </div>
        </div>

    </div>
</x-app-layout>