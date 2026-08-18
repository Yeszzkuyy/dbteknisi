<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Jadwal Teknisi</h1>
            <p class="text-slate-500 mt-1">Kalender jadwal pekerjaan teknisi terintegrasi dengan Google Calendar</p>
        </div>
        <div class="flex items-center gap-2">
            @if(session('sync_message'))
                <span class="text-xs text-slate-500 max-w-xs text-right">{{ session('sync_message') }}</span>
            @endif
            @if($connected)
                <x-status-badge color="green" icon="✓">
                    Google Calendar Terhubung
                </x-status-badge>
                @can('manage-teknisi')
                    <form action="{{ route('teknisi.kalender.disconnect') }}" method="POST"
                          onsubmit="return confirm('Putuskan koneksi Google Calendar? Jadwal tetap tersimpan di aplikasi.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-medium transition text-sm">
                            Putuskan Koneksi
                        </button>
                    </form>
                @endcan
            @else
                <x-status-badge color="slate">
                    Google Calendar Belum Terhubung
                </x-status-badge>
                @can('manage-teknisi')
                    <a href="{{ route('teknisi.kalender.connect') }}"
                       class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Hubungkan Google Calendar
                    </a>
                @endcan
            @endif
        </div>
    </div>

    {{-- Pencarian --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-4 mb-4 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-48">
            <x-icon name="edit" class="w-4 h-4 text-slate-400" />
            <input id="filter-search" type="search" placeholder="Cari jadwal, project, customer, teknisi..."
                   oninput="clearTimeout(window.__fcSearch); window.__fcSearch = setTimeout(() => window.teknisiCalendar?.calendar.refetchEvents(), 400)"
                   class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
    </div>

    {{-- Calendar --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-4">
        <div id="teknisi-calendar" class="teknisi-calendar" data-events-url="{{ route('teknisi.kalender.events') }}"></div>
    </div>
</div>

@vite(['resources/js/teknisi-calendar.js'])

@php
    $oldData = $errors->any()
        ? ['form' => old(), 'mode' => old('_schedule_mode', 'create'), 'schedule_id' => old('_schedule_id')]
        : null;
@endphp

<x-teknisi.schedule-modal
    :projects="$projects"
    :technicians="$technicians"
    :connected="$connected"
    :can-manage="auth()->user()->can('manage-teknisi')"
    :old-data="$oldData"
/>
</x-app-layout>