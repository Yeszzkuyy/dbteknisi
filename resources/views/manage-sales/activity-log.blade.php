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
            <ol class="relative border-l-2 border-slate-200 ml-3 space-y-6">
                @foreach($activities as $activity)
                    @php [$dotBg] = ($logColor)($activity->lead?->customer_id); @endphp
                    <li class="ml-6">
                        <span class="absolute -left-[9px] flex w-4 h-4 rounded-full" style="background: {{ $dotBg }}"></span>

                        <div class="flex flex-wrap items-center gap-x-2 gap-y-2 text-sm">
                            <x-user-avatar :user="$activity->user" size="w-7 h-7" text="text-xs" />
                            <span class="font-semibold text-slate-800">{{ $activity->user ? ($userNames[$activity->user_id] ?? $activity->user->name) : 'Sistem' }}</span>
                            <span class="text-slate-600">{{ $activity->actionLabel() }}</span>
                            @if($activity->lead && $activity->lead->customer)
                                @php [$badgeBg, $badgeText] = ($logColor)($activity->lead->customer_id); @endphp
                                — <a href="{{ route('manage-sales.edit', $activity->lead) }}"
                                     class="font-medium rounded-full px-2.5 py-0.5 hover:opacity-80 transition"
                                     style="background: {{ $badgeBg }}; color: {{ $badgeText }}">
                                    {{ $activity->lead->customer->name }}
                                </a>
                            @else
                                — <span class="text-slate-400 italic">lead sudah dihapus permanen</span>
                            @endif
                        </div>

                        @if($activity->changes)
                            <div class="mt-2 space-y-1">
                                @foreach($activity->changes as $field => $change)
                                    @php
                                        $hasOld = $change['old'] !== null && $change['old'] !== '';
                                        $old = ($formatLogValue)($field, $change['old']);
                                        $new = ($formatLogValue)($field, $change['new']);
                                    @endphp
                                    <div class="text-xs bg-slate-50 dark:bg-slate-800 rounded-lg px-3 py-2 inline-block mr-2">
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                                            {{ \App\Models\LeadActivity::FIELD_LABELS[$field] ?? $field }}:
                                        </span>
                                        @if($hasOld)
                                            <span class="text-red-500 line-through mr-1">{{ $old }}</span>
                                        @endif
                                        <span class="text-green-600 dark:text-green-400">{{ $new }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <p class="text-xs text-slate-400 mt-1">{{ $activity->created_at->format('d M Y, H:i') }} WIB</p>
                    </li>
                @endforeach
            </ol>

            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</x-app-layout>