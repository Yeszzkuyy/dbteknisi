<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Manajemen Pembayaran</h1>
            <p class="text-slate-500 mt-1">Kelola pembayaran dari invoice.</p>
        </div>
        @can('manage-admin')
            <a href="{{ route('admin.payments.create') }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                + Catat Pembayaran
            </a>
        @endcan
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Invoice Terkait</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Tgl Bayar</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Nominal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Bukti</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    @forelse($payments as $pm)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 font-mono text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $pm->invoice->invoice_number }}</td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $pm->invoice->customer->name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $pm->payment_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-slate-800 dark:text-slate-100">Rp {{ number_format($pm->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($pm->proof_file)
                                    <a href="{{ asset('storage/' . $pm->proof_file) }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-800 text-xs underline">Lihat</a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.payments.show', $pm) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                    @can('manage-admin')
                                        <form action="{{ route('admin.payments.destroy', $pm) }}" method="POST" onsubmit="return confirm('Hapus pembayaran ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-slate-400">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())<div class="p-4 border-t">{{ $payments->links() }}</div>@endif
    </div>
</x-app-layout>