<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Trash</h1>
                <p class="text-slate-500 mt-1">Data yang telah dihapus (soft delete)</p>
            </div>
            <a href="{{ route('customers.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-medium transition">
                ← Kembali
            </a>
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
                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Dihapus Pada</th>
                    <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $customer->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $customer->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $customer->deleted_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    @can('manage-admin')
                                        <form action="{{ route('trash.restore-customer', $customer->id) }}" 
                                              method="POST" 
                                              class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                Restore
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center text-slate-400">
                                    Tidak ada customer yang terhapus.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Dihapus Pada</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                        @forelse($projects as $project)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $project->project_name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $project->customer?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $project->deleted_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    @can('manage-admin')
                                        <form action="{{ route('trash.restore-project', $project->id) }}" 
                                              method="POST" 
                                              class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                Restore
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center text-slate-400">
                                    Tidak ada project yang terhapus.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>