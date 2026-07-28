<x-app-layout>
    @php
        $tab = request('tab', 'invoices');
    @endphp
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Invoice &amp; PO Manager</h1>
        <p class="text-slate-500 mt-1">Kelola invoice, purchase order, dan pembayaran.</p>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ $tab }}' }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-200">
            <nav class="flex gap-4 sm:gap-6 px-4 sm:px-6 overflow-x-auto whitespace-nowrap -mb-px">
                <a href="{{ route('admin.invoices.index') }}"
                   @click.prevent="tab = 'invoices'; $el.closest('a').click()"
                   :class="{ 'border-indigo-500 text-indigo-600': tab === 'invoices', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'invoices' }"
                   class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition cursor-pointer">
                    Invoice
                </a>
                <a href="{{ route('admin.pos.index') }}"
                   @click.prevent="tab = 'pos'; $el.closest('a').click()"
                   :class="{ 'border-indigo-500 text-indigo-600': tab === 'pos', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'pos' }"
                   class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition cursor-pointer">
                    Purchase Order
                </a>
                <a href="{{ route('admin.payments.index') }}"
                   @click.prevent="tab = 'payments'; $el.closest('a').click()"
                   :class="{ 'border-indigo-500 text-indigo-600': tab === 'payments', 'border-transparent text-slate-500 hover:text-slate-700': tab !== 'payments' }"
                   class="shrink-0 py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm transition cursor-pointer">
                    Payment
                </a>
            </nav>
        </div>

        <div class="p-4 sm:p-6">
            {{-- INVOICES --}}
            <div x-show="tab === 'invoices'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Invoice</h3>
                    @can('manage-admin')
                        <a href="{{ route('admin.invoices.create') }}"
                           class="px-4 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">
                            + Buat Invoice
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">No Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Nominal</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($invoices as $inv)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-mono text-sm font-semibold text-slate-800">{{ $inv->invoice_number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $inv->customer->name }}</td>
                                    <td class="px-4 py-3 text-right font-mono text-slate-800">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
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

            {{-- PO --}}
            <div x-show="tab === 'pos'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Purchase Order</h3>
                    @can('manage-admin')
                        <a href="{{ route('admin.pos.create') }}"
                           class="px-4 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">
                            + Buat PO
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">No PO</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pos as $po)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-mono text-sm font-semibold text-slate-800">{{ $po->po_number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $po->customer->name }}</td>
                                    <td class="px-4 py-3 text-slate-600 max-w-xs truncate">{{ Str::limit($po->items, 60) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                            @if($po->status === 'selesai') bg-green-100 text-green-700
                                            @elseif($po->status === 'dibatalkan') bg-red-100 text-red-700
                                            @elseif($po->status === 'diproses') bg-yellow-100 text-yellow-700
                                            @else bg-slate-100 text-slate-600 @endif">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.pos.show', $po) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                            @can('manage-admin')
                                                <a href="{{ route('admin.pos.edit', $po) }}" class="text-amber-600 hover:text-amber-800 text-sm">Edit</a>
                                                <form action="{{ route('admin.pos.destroy', $po) }}" method="POST" onsubmit="return confirm('Hapus PO ini?')" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-8 text-center text-slate-400">Belum ada PO.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pos->hasPages())<div class="p-4 border-t">{{ $pos->links() }}</div>@endif
            </div>

            {{-- PAYMENTS --}}
            <div x-show="tab === 'payments'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Pembayaran</h3>
                    @can('manage-admin')
                        <a href="{{ route('admin.payments.create') }}"
                           class="px-4 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">
                            + Catat Pembayaran
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Invoice Terkait</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tgl Bayar</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Nominal</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Bukti</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($payments as $pm)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-mono text-sm font-semibold text-slate-800">{{ $pm->invoice->invoice_number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $pm->invoice->customer->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $pm->payment_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-right font-mono text-slate-800">Rp {{ number_format($pm->amount, 0, ',', '.') }}</td>
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
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sync tab from URL on page load
            const urlParams = new URLSearchParams(window.location.search);
            const tabFromUrl = urlParams.get('tab');
            if (tabFromUrl) {
                document.querySelectorAll('[x-data]').forEach(el => {
                    if (el.__x) el.__x.$data.tab = tabFromUrl;
                });
            }
        });
    </script>
</x-app-layout>
