<x-app-layout>
    @php $tab = request('tab', 'payments'); @endphp
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Invoice &amp; PO Manager</h1>
        <p class="text-slate-500 mt-1">Kelola invoice, purchase order, dan pembayaran.</p>
    </div>
    <div x-data="{ tab: '{{ $tab }}' }" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-slate-600">
            <nav class="flex gap-4 sm:gap-6 px-4 sm:px-6 overflow-x-auto whitespace-nowrap -mb-px">
                <a href="{{ route('admin.invoices.index') }}" @click.prevent="tab = 'invoices'; window.location='{{ route('admin.invoices.index') }}'"
                   :class="{ 'border-indigo-500 text-indigo-600': tab === 'invoices', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'invoices' }"
                   class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition cursor-pointer">Invoice</a>
                <a href="{{ route('admin.pos.index') }}" @click.prevent="tab = 'pos'; window.location='{{ route('admin.pos.index') }}'"
                   :class="{ 'border-indigo-500 text-indigo-600': tab === 'pos', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'pos' }"
                   class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition cursor-pointer">Purchase Order</a>
                <a href="{{ route('admin.payments.index') }}" @click.prevent="tab = 'payments'; window.location='{{ route('admin.payments.index') }}'"
                   :class="{ 'border-indigo-500 text-indigo-600': tab === 'payments', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'payments' }"
                   class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition cursor-pointer">Payment</a>
            </nav>
        </div>
        <div class="p-4 sm:p-6">
            <div x-show="tab === 'payments'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Pembayaran</h3>
                    @can('manage-admin')<a href="{{ route('admin.payments.create') }}" class="px-4 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">+ Catat Pembayaran</a>@endcan
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                        <thead class="bg-slate-50 dark:bg-slate-700">
                            <tr><th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Invoice Terkait</th><th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Customer</th><th class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Tgl Bayar</th><th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Nominal</th><th class="px-4 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Bukti</th><th class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                            @forelse($payments as $pm)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono text-sm font-semibold text-slate-800">{{ $pm->invoice->invoice_number }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $pm->invoice->customer->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $pm->payment_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right font-mono text-slate-800">Rp {{ number_format($pm->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">@if($pm->proof_file)<a href="{{ asset('storage/' . $pm->proof_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs underline">Lihat</a>@else<span class="text-slate-400 text-xs">-</span>@endif</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.payments.show', $pm) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                        @can('manage-admin')<form action="{{ route('admin.payments.destroy', $pm) }}" method="POST" onsubmit="return confirm('Hapus pembayaran ini?')" class="inline">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 text-sm">Hapus</button></form>@endcan
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
        </div>
    </div>
</x-app-layout>
