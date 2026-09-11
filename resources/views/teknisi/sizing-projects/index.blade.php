<x-app-layout>
@php
    $statuses = [
        'draft' => 'Draft',
        'in_progress' => 'In Progress',
        'waiting_approval' => 'Waiting Approval',
        'completed' => 'Completed',
    ];

    $statusColors = [
        'draft' => 'slate',
        'in_progress' => 'yellow',
        'waiting_approval' => 'orange',
        'completed' => 'green',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Sizing Projects</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Kelola hasil sizing / usulan solusi per project</p>
        </div>
        <a href="{{ route('teknisi.sizing-projects.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium">
            + Tambah Sizing
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('teknisi.sizing-projects.index') }}"
          class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-4 mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
            <select name="status"
                    class="rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Project</label>
            <select name="project_id"
                    class="rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Project</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>
                        {{ $project->project_name }}
                        @if ($project->customer) — {{ $project->customer->name }} @endif
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium">
            Filter
        </button>

        @if (request()->filled('status') || request()->filled('project_id'))
            <a href="{{ route('teknisi.sizing-projects.index') }}"
               class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 rounded-xl text-sm">
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        @php
                            $headers = ['Project', 'Customer', 'Sales PIC', 'Quantity', 'Status', 'Aksi'];
                        @endphp
                        @foreach ($headers as $header)
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-600">
                    @forelse ($sizings as $sizing)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 text-sm text-slate-800 dark:text-slate-100">
                                {{ $sizing->project->project_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300">
                                {{ $sizing->project?->customer?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300">
                                {{ $sizing->sales_pic ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300">
                                {{ $sizing->quantity ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge color="{{ $statusColors[$sizing->status] ?? 'slate' }}">
                                    {{ $statuses[$sizing->status] ?? $sizing->status }}
                                </x-status-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('teknisi.sizing-projects.show', $sizing) }}"
                                       class="px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700">
                                        Lihat
                                    </a>
                                    <a href="{{ route('teknisi.sizing-projects.edit', $sizing) }}"
                                       class="px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-100 hover:bg-blue-200 text-blue-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('teknisi.sizing-projects.destroy', $sizing) }}" method="POST"
                                          onsubmit="return confirm('Hapus sizing project ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-600 hover:bg-red-700 text-white">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state label="sizing project" description="Belum ada data sizing." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>