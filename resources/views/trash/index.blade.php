<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">{{ $isSuperAdmin ? 'All Trash' : 'Trash Saya' }}</h1>
                <p class="text-slate-500 mt-1">{{ $isSuperAdmin ? 'Seluruh data terhapus dari semua user' : 'Data yang telah kamu hapus (soft delete)' }}</p>
            </div>

            <div class="flex items-center gap-3">
                @if($isSuperAdmin && $users)
                    <form method="GET" action="{{ route('trash.index') }}">
                        <select name="user" onchange="this.form.submit()"
                                class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
                @can('manage-admin')
                    <form action="{{ route('trash.clear') }}" method="POST"
                          onsubmit="return confirm('Yakin bersihkan semua trash? Data tidak bisa dikembalikan.')">
                        @csrf
                        @method('DELETE')
                        <button class="px-5 py-2.5 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 font-medium transition">
                            Bersihkan Trash
                        </button>
                    </form>
                @endcan
                <a href="{{ route('customers.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 font-medium transition">
                    ← Kembali
                </a>
            </div>
        </div>

        {{-- Customer Trash --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700">
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200">Customer Terhapus</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Nama Customer</th>
                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Email</th>
                    @if($isSuperAdmin)<th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Dihapus Oleh</th>@endif
                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Dihapus Pada</th>
                    <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $customer->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $customer->email ?? '-' }}</td>
                                @if($isSuperAdmin)<td class="px-6 py-4 text-slate-600">{{ $customer->deleter?->name ?? '-' }}</td>@endif
                                <td class="px-6 py-4 text-slate-600">{{ $customer->deleted_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    @can('manage-admin')
                                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                            <form action="{{ route('trash.restore-customer', $customer->id) }}"
                                                  method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="Restore" aria-label="Restore"
                                                        class="inline-flex items-center justify-center shrink-0 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-500/10 hover:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:text-emerald-400 p-1.5 transition-colors duration-200">
                                                    <x-icon name="restore" class="w-4 h-4" />
                                                </button>
                                            </form>
                                            <button type="button" x-data=""
                                                    title="Hapus Permanen" aria-label="Hapus Permanen"
                                                    @click="$dispatch('open-modal', 'confirm-destroy-customer-{{ $customer->id }}')"
                                                    class="inline-flex items-center justify-center shrink-0 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-500/10 hover:bg-red-500/10 text-red-600 dark:text-red-400 hover:text-red-400 p-1.5 transition-colors duration-200">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSuperAdmin ? 5 : 4 }}" class="py-16 text-center text-slate-400">
                                    Tidak ada customer yang terhapus.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @can('manage-admin')
            @foreach($customers as $customer)
                <x-modal name="confirm-destroy-customer-{{ $customer->id }}" maxWidth="md">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <x-icon name="trash" class="w-5 h-5 text-red-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Hapus Permanen?</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Customer "{{ $customer->name }}" akan dihapus <b>selamanya</b> dan tidak bisa dikembalikan.
                                </p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="$dispatch('close')"
                                    class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 text-sm font-medium transition-colors duration-200">
                                Batal
                            </button>
                            <form action="{{ route('trash.destroy-customer', $customer->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm font-medium transition-colors duration-200">
                                    <x-icon name="trash" class="w-4 h-4" />
                                    Ya, Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </div>
                </x-modal>
            @endforeach
        @endcan

        {{-- Project Trash --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700">
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200">Project Terhapus</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Nama Project</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Customer</th>
                            @if($isSuperAdmin)<th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Dihapus Oleh</th>@endif
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Dihapus Pada</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                        @forelse($projects as $project)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $project->project_name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $project->customer?->name ?? '-' }}</td>
                                @if($isSuperAdmin)<td class="px-6 py-4 text-slate-600">{{ $project->deleter?->name ?? '-' }}</td>@endif
                                <td class="px-6 py-4 text-slate-600">{{ $project->deleted_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    @can('manage-admin')
                                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                            <form action="{{ route('trash.restore-project', $project->id) }}"
                                                  method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="Restore" aria-label="Restore"
                                                        class="inline-flex items-center justify-center shrink-0 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-500/10 hover:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:text-emerald-400 p-1.5 transition-colors duration-200">
                                                    <x-icon name="restore" class="w-4 h-4" />
                                                </button>
                                            </form>
                                            <button type="button" x-data=""
                                                    title="Hapus Permanen" aria-label="Hapus Permanen"
                                                    @click="$dispatch('open-modal', 'confirm-destroy-project-{{ $project->id }}')"
                                                    class="inline-flex items-center justify-center shrink-0 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-500/10 hover:bg-red-500/10 text-red-600 dark:text-red-400 hover:text-red-400 p-1.5 transition-colors duration-200">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSuperAdmin ? 5 : 4 }}" class="py-16 text-center text-slate-400">
                                    Tidak ada project yang terhapus.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @can('manage-admin')
            @foreach($projects as $project)
                <x-modal name="confirm-destroy-project-{{ $project->id }}" maxWidth="md">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <x-icon name="trash" class="w-5 h-5 text-red-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Hapus Permanen?</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Project "{{ $project->project_name }}" akan dihapus <b>selamanya</b> dan tidak bisa dikembalikan.
                                </p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="$dispatch('close')"
                                    class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 text-sm font-medium transition-colors duration-200">
                                Batal
                            </button>
                            <form action="{{ route('trash.destroy-project', $project->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm font-medium transition-colors duration-200">
                                    <x-icon name="trash" class="w-4 h-4" />
                                    Ya, Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </div>
                </x-modal>
            @endforeach
        @endcan
    </div>
</x-app-layout>