<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Data Partner</h1>
            <p class="text-slate-500 mt-1">Kelola supplier, vendor, kontraktor, partner, dan distributor.</p>
        </div>
        @can('manage-marketing')
            <a href="{{ route('partners.create') }}"
               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                + Tambah Partner
            </a>
        @endcan
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="p-5 border-b dark:border-slate-600 bg-slate-50 dark:bg-slate-700">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-500 dark:text-slate-300">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama, kontak, telepon..."
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500 dark:text-slate-300">Tipe</label>
                    <select name="type" class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        @foreach(\App\Models\Partner::TYPES as $val => $label)
                            <option value="{{ $val }}" {{ request('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-2 flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Filter
                    </button>
                    <a href="{{ route('partners.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    @forelse($partners as $partner)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100">{{ $partner->name }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                    @switch($partner->type)
                                        @case('supplier') bg-blue-100 text-blue-800 @break
                                        @case('vendor') bg-purple-100 text-purple-800 @break
                                        @case('kontraktor') bg-orange-100 text-orange-800 @break
                                        @case('partner') bg-green-100 text-green-800 @break
                                        @case('distributor') bg-yellow-100 text-yellow-800 @break
                                        @default bg-slate-100 text-slate-800
                                    @endswitch">
                                    {{ \App\Models\Partner::TYPES[$partner->type] ?? $partner->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $partner->contact_person ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $partner->phone ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $partner->email ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    @can('manage-marketing')
                                        <a href="{{ route('partners.edit', $partner) }}" class="text-amber-600 hover:text-amber-800 text-sm">Edit</a>
                                        <form action="{{ route('partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Hapus partner ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Belum ada data partner.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($partners->hasPages())
            <div class="px-6 py-4 border-t">{{ $partners->links() }}</div>
        @endif
    </div>
</x-app-layout>