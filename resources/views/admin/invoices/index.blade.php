<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Manajemen Invoice</h1>
            <p class="text-slate-500 mt-1">Kelola invoice untuk customer.</p>
        </div>
        @can('manage-admin')
            <a href="{{ route('admin.invoices.create') }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                + Buat Invoice
            </a>
        @endcan
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">No Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Customer</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Nominal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 font-mono text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $inv->customer->name }}</td>
                            <td class="px-4 py-3 text-right font-mono text-slate-800 dark:text-slate-100">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                    @if($inv->status === 'paid') bg-green-100 text-green-700
                                    @elseif($inv->status === 'cancelled') bg-red-100 text-red-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ $inv->status === 'paid' ? 'Lunas' : ($inv->status === 'cancelled' ? 'Dibatalkan' : 'Belum Bayar') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.invoices.show', $inv) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                    @can('manage-admin')
                                        <a href="{{ route('admin.invoices.edit', $inv) }}" class="text-amber-600 hover:text-amber-800 text-sm">Edit</a>
                                        <form action="{{ route('admin.invoices.destroy', $inv) }}" method="POST" onsubmit="return confirm('Hapus invoice ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-400">Belum ada invoice.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())<div class="p-4 border-t">{{ $invoices->links() }}</div>@endif
    </div>
</x-app-layout>