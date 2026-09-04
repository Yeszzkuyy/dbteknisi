<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Activity Log Management</h1>
            <p class="text-slate-500 mt-1">
                Riwayat aktivitas user management (assign & kelola lead)
                @if($filterUser)
                    — filter: <span class="font-semibold text-blue-600">{{ $filterUser->name }}</span>
                    <a href="{{ route('manage-sales.activity-log') }}" class="text-slate-400 hover:text-red-500 ml-1" title="Hapus filter">&times;</a>
                @endif
            </p>
        </div>
        <a href="{{ route('manage-sales.index') }}"
           class="px-4 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="p-4 mb-4 rounded-xl bg-slate-50 border border-slate-200">
            <form method="GET" action="{{ route('manage-sales.activity-log') }}" class="flex flex-wrap gap-4">
                <div>
                    <label for="user" class="sr-only">User</label>
                    <select id="user" name="user" class="px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua User</option>
                        @foreach($managementUsers as $user)
                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="sr-only">Dari Tanggal</label>
                    <x-datepicker id="date_from" name="date_from" value="{{ request('date_from') }}"></x-datepicker>
                </div>
                <div>
                    <label for="date_to" class="sr-only">Sampai Tanggal</label>
                    <x-datepicker id="date_to" name="date_to" value="{{ request('date_to') }}"></x-datepicker>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filter</button>
                <a href="{{ route('manage-sales.activity-log') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">Reset</a>
            </form>
        </div>

        @php
            $actionMeta = [
                'created' => [
                    'color' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300',
                    'icon' => '<path d="M12 5v14M5 12h14"/>',
                ],
                'updated' => [
                    'color' => 'bg-slate-200 text-slate-500 dark:bg-slate-600 dark:text-slate-300',
                    'icon' => '<path d="M4 20h4.2L18.5 9.7a2.1 2.1 0 000-3l-1.2-1.2a2.1 2.1 0 00-3 0L4 15.8V20z"/><path d="M13.5 6.5l4 4"/>',
                ],
                'deleted' => [
                    'color' => 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300',
                    'icon' => '<path d="M5 7h14"/><path d="M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2"/><path d="M6.5 7l.8 12a2 2 0 002 1.9h5.4a2 2 0 002-1.9l.8-12"/><path d="M10 11v6"/><path d="M14 11v6"/>',
                ],
                'converted' => [
                    'color' => 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300',
                    'icon' => '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/>',
                ],
                'status_changed' => [
                    'color' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300',
                    'icon' => '<path d="M4 7h12M12 3l4 4-4 4"/><path d="M20 17H8M12 13l-4 4 4 4"/>',
                ],
                'attachment_deleted' => [
                    'color' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/40 dark:text-orange-300',
                    'icon' => '<path d="M9 12V7a3.5 3.5 0 017 0v8a2.5 2.5 0 01-5 0V9"/>',
                ],
                'assigned' => [
                    'color' => 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300',
                    'icon' => '<circle cx="10" cy="8" r="3.5"/><path d="M4 20c0-3.5 2.7-6 6-6"/><path d="M18 13v6M15 16h6"/>',
                ],
                'default' => [
                    'color' => 'bg-slate-200 text-slate-500 dark:bg-slate-600 dark:text-slate-300',
                    'icon' => '<path d="M4 20h4.2L18.5 9.7a2.1 2.1 0 000-3l-1.2-1.2a2.1 2.1 0 00-3 0L4 15.8V20z"/><path d="M13.5 6.5l4 4"/>',
                ],
            ];

            $roleLabels = ['super-admin' => 'Super Admin', 'marketing-lead' => 'Marketing Lead'];
            $roleLabel = function ($user) use ($roleLabels): string {
                $name = $user?->roles?->first()?->name;
                if (! $name) {
                    return 'Sistem';
                }

                return $roleLabels[$name] ?? ucfirst(str_replace('-', ' ', $name));
            };

            $dateLabel = function (string $date): string {
                $d = \Carbon\Carbon::parse($date);
                if ($d->isToday()) {
                    return 'Hari Ini';
                }
                if ($d->isYesterday()) {
                    return 'Kemarin';
                }

                return $d->translatedFormat('d F Y');
            };

            $logColor = function (?int $customerId): array {
                if (! $customerId) {
                    return ['hsl(220, 10%, 90%)', 'hsl(220, 10%, 35%)'];
                }
                $hue = fmod($customerId * 137.5, 360);

                return ["hsl({$hue}, 70%, 90%)", "hsl({$hue}, 70%, 30%)"];
            };

            $formatLogValue = function (string $field, $value) use ($customerNames, $userNames): string {
                if ($value === null || $value === '') {
                    return '-';
                }
                if ($field === 'customer_id') {
                    return (string) ($customerNames[$value] ?? $value);
                }
                if ($field === 'assigned_to') {
                    return (string) ($userNames[$value] ?? $value);
                }
                if (in_array($field, ['pt_group', 'segment', 'source', 'status'])) {
                    return \App\Http\Controllers\LeadController::label((string) $value);
                }
                if ($field === 'incoming_date') {
                    return \Carbon\Carbon::parse($value)->translatedFormat('d M Y');
                }

                return (string) $value;
            };
        @endphp

        @if($activities->isEmpty())
            <div class="text-center py-12 px-4">
                <p class="text-sm font-medium text-slate-500">Belum ada aktivitas management.</p>
            </div>
        @else
            @foreach($grouped as $date => $items)
                <div class="relative">
                    {{-- Garis vertikal timeline (per grup tanggal) --}}
                    <div class="absolute left-4 top-0 bottom-0 w-px bg-slate-200 dark:bg-slate-600"></div>

                    {{-- Header kelompok tanggal --}}
                    <div class="relative mb-5">
                        <span class="absolute left-4 top-1/2 -translate-x-1/2 flex w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-500 ring-4 ring-white dark:ring-slate-900"></span>
                        <div class="ml-12 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            {{ $dateLabel($date) }}
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-[10px] font-semibold">{{ count($items) }} aktivitas</span>
                        </div>
                    </div>

                    <div class="space-y-4 pb-6">
                        @foreach($items as $activity)
                            @php
                                $meta = $actionMeta[$activity->action] ?? $actionMeta['default'];
                                [$customerBadgeBg, $customerBadgeText] = ($logColor)($activity->lead?->customer_id);
                                $firstField = $activity->changes ? array_key_first($activity->changes) : null;
                                $firstChange = $firstField !== null ? $activity->changes[$firstField] : null;
                                $firstOld = $firstChange && $firstChange['old'] !== null && $firstChange['old'] !== ''
                                    ? ($formatLogValue)($firstField, $firstChange['old']) : null;
                                $firstNew = $firstChange ? ($formatLogValue)($firstField, $firstChange['new']) : null;
                            @endphp
                            <div x-data="{ open: false }" class="relative">
                                {{-- Ikon indikator aksi di garis timeline --}}
                                <div class="absolute left-4 top-5 -translate-x-1/2 flex w-8 h-8 items-center justify-center rounded-full {{ $meta['color'] }} ring-4 ring-white dark:ring-slate-900 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        {!! $meta['icon'] !!}
                                    </svg>
                                </div>

                                <div class="ml-12 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-600 shadow-sm p-4">
                                    <div class="flex items-start gap-4">
                                        {{-- KIRI: avatar + nama + role --}}
                                        <div class="w-16 shrink-0 flex flex-col items-center gap-1 text-center">
                                            <x-user-avatar :user="$activity->user" size="w-10 h-10" text="text-sm" />
                                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate w-full" title="{{ $activity->user?->name ?? 'Sistem' }}">{{ $activity->user?->name ?? 'Sistem' }}</span>
                                            <span class="text-[10px] uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $roleLabel($activity->user) }}</span>
                                        </div>

                                        {{-- TENGAH: rincian aksi + lead/customer + waktu --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
                                                <span class="text-slate-700 dark:text-slate-200">
                                                    <span class="font-bold">{{ $activity->user?->name ?? 'Sistem' }}</span>
                                                    {{ $activity->actionLabel() }}
                                                </span>
                                                @if($activity->lead && $activity->lead->customer)
                                                    <a href="{{ route('manage-sales.edit', $activity->lead) }}"
                                                       class="font-medium rounded-full px-2.5 py-0.5 hover:opacity-80 transition"
                                                       style="background: {{ $customerBadgeBg }}; color: {{ $customerBadgeText }}">
                                                        {{ $activity->lead->customer->name }}
                                                    </a>
                                                @else
                                                    <span class="text-slate-400 italic text-xs">lead sudah dihapus permanen</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-400 mt-1">{{ $activity->created_at->format('H:i') }} WIB</p>
                                            @if($firstChange)
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate">
                                                    <span class="font-medium">{{ \App\Models\LeadActivity::FIELD_LABELS[$firstField] ?? $firstField }}:</span>
                                                    @if($firstOld)
                                                        <span class="line-through text-red-400 mr-1">{{ $firstOld }}</span>
                                                    @endif
                                                    <span class="text-green-600 dark:text-green-400">{{ $firstNew }}</span>
                                                </p>
                                            @endif
                                        </div>

                                        {{-- KANAN: tombol accordion --}}
                                        @if($activity->changes)
                                            <button type="button" x-on:click="open = !open"
                                                    class="shrink-0 p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                                                    title="Detail perubahan" aria-label="Detail perubahan">
                                                <svg class="w-5 h-5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Detail perubahan lengkap (accordion) --}}
                                    @if($activity->changes)
                                        <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="border-t border-slate-100 dark:border-slate-600 mt-3 pt-3">
                                            <div class="space-y-1.5">
                                                @foreach($activity->changes as $field => $change)
                                                    @php
                                                        $hasOld = $change['old'] !== null && $change['old'] !== '';
                                                        $old = ($formatLogValue)($field, $change['old']);
                                                        $new = ($formatLogValue)($field, $change['new']);
                                                    @endphp
                                                    <div class="flex flex-wrap items-start gap-x-2 gap-y-1 text-xs">
                                                        <span class="font-semibold text-slate-600 dark:text-slate-300 min-w-[110px]">
                                                            {{ \App\Models\LeadActivity::FIELD_LABELS[$field] ?? $field }}
                                                        </span>
                                                        @if($hasOld)
                                                            <span class="text-red-500 line-through">{{ $old }}</span>
                                                        @endif
                                                        <span class="text-green-600 dark:text-green-400">→ {{ $new }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="mt-2">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</x-app-layout>