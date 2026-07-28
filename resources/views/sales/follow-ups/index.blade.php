<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Follow Up Customer</h1>
            <p class="text-slate-500 mt-1">Pantau tindak lanjut dengan customer.</p>
        </div>
        @can('manage-sales')
            <a href="{{ route('sales.follow-ups.create') }}"
               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                + Tambah Follow Up
            </a>
        @endcan
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Cari Customer</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama customer..."
                       class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search']))
                    <a href="{{ route('sales.follow-ups.index') }}"
                       class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase">Terkait Meeting</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase">Tanggal Follow Up</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase">Oleh</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($followUps as $fu)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $fu->customer->name }}</td>
                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                                {{ Str::limit($fu->description, 100) }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $fu->meeting ? $fu->meeting->meeting_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $fu->follow_up_date ? $fu->follow_up_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $fu->creator?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('sales.follow-ups.show', $fu) }}"
                                       class="text-blue-600 hover:text-blue-800">Detail</a>
                                    @can('manage-sales')
                                        <a href="{{ route('sales.follow-ups.edit', $fu) }}"
                                           class="text-amber-600 hover:text-amber-800">Edit</a>
                                        <form action="{{ route('sales.follow-ups.destroy', $fu) }}"
                                              method="POST" onsubmit="return confirm('Hapus follow up ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">Belum ada follow up.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($followUps->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $followUps->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
