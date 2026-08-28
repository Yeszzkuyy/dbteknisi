<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Data Partner</h1>
            <p class="text-slate-500 mt-1">{{ $partners->total() }} partner terdaftar &mdash; supplier, vendor, kontraktor, partner, dan distributor</p>
        </div>
        @can('manage-marketing')
            <a href="{{ route('partners.create') }}"
               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition inline-flex items-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Partner
            </a>
        @endcan
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">

        {{-- Toolbar --}}
        <form method="GET" class="p-5 border-b border-slate-200 dark:border-slate-600 flex flex-col sm:flex-row gap-3 sm:items-center">
            <div class="relative flex-1 min-w-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, kontak, atau telepon..."
                       class="w-full pl-10 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <select name="type"
                    class="sm:w-44 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Tipe</option>
                @foreach(\App\Models\Partner::TYPES as $val => $label)
                    <option value="{{ $val }}" {{ request('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                    Filter
                </button>
                <a href="{{ route('partners.index') }}"
                   class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 text-sm font-medium transition">
                    Reset
                </a>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Partner</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Jumlah Lead</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600/70">
                    @forelse($partners as $partner)
                        @php
                            $dotColor = \App\Models\Partner::TYPE_DOTS[$partner->type] ?? '#94a3b8';
                            $palette = ['#3b82f6', '#8b5cf6', '#14b8a6', '#f43f5e', '#f59e0b', '#10b981'];
                            $avatarBg = $palette[abs(crc32($partner->name)) % count($palette)];
                            $initials = collect(explode(' ', trim($partner->name)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                ->implode('');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                                          style="background-color: {{ $avatarBg }}">{{ $initials }}</span>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800 dark:text-slate-100 truncate max-w-[220px]">{{ $partner->name }}</div>
                                        @if($partner->address)
                                            <div class="text-xs text-slate-400 truncate max-w-[240px]">{{ $partner->address }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium
                                             bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $dotColor }}"></span>
                                    {{ \App\Models\Partner::TYPES[$partner->type] ?? $partner->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($partner->contact_person || $partner->phone || $partner->email)
                                    <div class="space-y-0.5">
                                        @if($partner->contact_person)
                                            <div class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $partner->contact_person }}</div>
                                        @endif
                                        @if($partner->phone)
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $partner->phone }}</div>
                                        @endif
                                        @if($partner->email)
                                            <div class="text-xs text-slate-400 truncate max-w-[220px]">{{ $partner->email }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($partner->leads_count > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $partner->leads_count }} lead
                                    </span>
                                @else
                                    <span class="text-slate-400 text-sm">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                    @can('manage-marketing')
                                        <a href="{{ route('partners.edit', $partner) }}" title="Edit partner"
                                           class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('partners.destroy', $partner) }}" method="POST"
                                              onsubmit="return confirm('Hapus partner {{ $partner->name }}?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus partner"
                                                    class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="mx-auto w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-slate-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">
                                    {{ request()->filled('search') || request()->filled('type') ? 'Tidak ada partner yang cocok dengan filter.' : 'Belum ada data partner.' }}
                                </p>
                                @can('manage-marketing')
                                    <a href="{{ route('partners.create') }}"
                                       class="inline-flex mt-4 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                                        + Tambah Partner Pertama
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($partners->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600">{{ $partners->links() }}</div>
        @endif
    </div>
</x-app-layout>
