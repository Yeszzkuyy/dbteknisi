<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Log Aktivitas Lead</h1>
            <p class="text-slate-500 mt-1">
                Riwayat semua perubahan lead beserta user yang melakukannya
                @if($filterUser)
                    — filter: <span class="font-semibold text-blue-600">{{ $filterUser->name }}</span>
                    <a href="{{ route('leads.activities') }}" class="text-slate-400 hover:text-red-500 ml-1" title="Hapus filter">&times;</a>
                @endif
            </p>
        </div>
        <a href="{{ route('leads.index') }}"
           class="px-4 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        @if($activities->isEmpty())
            <div class="text-center py-12 px-4">
                <p class="text-sm font-medium text-slate-500">Belum ada aktivitas lead.</p>
            </div>
        @else
            <ol class="relative border-l-2 border-slate-200 ml-3 space-y-6">
                @foreach($activities as $activity)
                    <li class="ml-6">
                        <span class="absolute -left-[9px] flex w-4 h-4 rounded-full
                            @switch($activity->action)
                                @case('created') bg-green-500 @break
                                @case('updated') bg-blue-500 @break
                                @case('deleted') bg-red-500 @break
                                @case('converted') bg-purple-500 @break
                                @default bg-slate-400
                            @endswitch"></span>

                        <div class="flex flex-wrap items-center gap-x-2 text-sm">
                            <span class="font-semibold text-slate-800">{{ $activity->user?->name ?? 'Sistem' }}</span>
                            <span class="text-slate-600">{{ $activity->actionLabel() }}</span>
                            @if($activity->lead && $activity->lead->customer)
                                — <a href="{{ route('leads.show', $activity->lead) }}"
                                     class="font-medium text-blue-600 hover:underline">
                                    {{ $activity->lead->customer->name }}
                                </a>
                            @else
                                — <span class="text-slate-400 italic">lead sudah dihapus permanen</span>
                            @endif
                        </div>

                        @if($activity->changes)
                            <div class="mt-2 space-y-1">
                                @foreach($activity->changes as $field => $change)
                                    <div class="text-xs bg-slate-50 dark:bg-slate-800 rounded-lg px-3 py-2 inline-block mr-2">
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                                            {{ \App\Models\LeadActivity::FIELD_LABELS[$field] ?? $field }}:
                                        </span>
                                        <span class="text-red-500 line-through mr-1">
                                            {{ is_scalar($change['old']) ? ($change['old'] ?: '-') : json_encode($change['old']) }}
                                        </span>
                                        <span class="text-green-600 dark:text-green-400">
                                            {{ is_scalar($change['new']) ? ($change['new'] ?: '-') : json_encode($change['new']) }}
                                        </span>
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
