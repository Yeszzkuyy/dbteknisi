<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Detail Meeting</h1>
                <p class="text-slate-500 mt-1">{{ $meeting->customer->name }} · {{ $meeting->meeting_date->format('d M Y') }}</p>
            </div>
            <div class="flex gap-2">
                @can('manage-sales')
                    <a href="{{ route('sales.meetings.edit', $meeting) }}"
                       class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium transition">
                        Edit
                    </a>
                @endcan
                <a href="{{ route('sales.meetings.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-blue-400 hover:bg-blue-500 text-white text-sm font-medium transition">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Main Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 lg:col-span-2">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Informasi Meeting</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-slate-400">Customer</dt>
                        <dd class="font-medium text-slate-800">{{ $meeting->customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Tanggal Meeting</dt>
                        <dd class="font-medium text-slate-800">{{ $meeting->meeting_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Peserta</dt>
                        <dd class="font-medium text-slate-800">{{ $meeting->participants ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Dicatat oleh</dt>
                        <dd class="font-medium text-slate-800">{{ $meeting->creator?->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- User Needs --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Kebutuhan User</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $meeting->user_needs ?? '-' }}</p>
            </div>

            {{-- User Complaints --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Keluhan User</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $meeting->user_complaints ?? '-' }}</p>
            </div>

            {{-- Existing System --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Sistem Existing</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $meeting->existing_system ?? '-' }}</p>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Catatan</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $meeting->notes ?? '-' }}</p>
            </div>

            {{-- Follow Ups --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Follow Up</h3>
                    @can('manage-sales')
                        <a href="{{ route('sales.follow-ups.create', ['customer_id' => $meeting->customer_id, 'meeting_id' => $meeting->id]) }}"
                           class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition">
                            + Tambah Follow Up
                        </a>
                    @endcan
                </div>
                @forelse($meeting->followUps as $followUp)
                    <div class="flex items-start gap-3 py-3 border-b border-slate-100 last:border-0">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-green-600 font-semibold text-xs">
                                {{ strtoupper(substr($followUp->creator?->name ?? 'F', 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-slate-800">{{ $followUp->description }}</p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $followUp->creator?->name ?? 'System' }}
                                @if($followUp->follow_up_date)
                                    · {{ $followUp->follow_up_date->format('d M Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Belum ada follow up.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
