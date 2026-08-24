<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">{{ $invoice->invoice_number }}</h1>
                <p class="text-slate-500 mt-1">{{ $invoice->customer->name }}</p>
            </div>
            <div class="flex gap-2">
                @can('manage-admin')
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium transition">Edit</a>
                @endcan
                <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2.5 rounded-xl bg-blue-400 hover:bg-blue-500 text-white text-sm font-medium transition">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Informasi Invoice</h3>
                <dl class="space-y-3">
                    <div><dt class="text-xs text-slate-400">No Invoice</dt><dd class="font-mono font-semibold text-slate-800">{{ $invoice->invoice_number }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Customer</dt><dd class="font-medium text-slate-800">{{ $invoice->customer->name }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Proyek</dt><dd class="font-medium text-slate-800">{{ $invoice->project?->project_name ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Tanggal Terbit</dt><dd class="font-medium text-slate-800">{{ $invoice->issue_date->format('d M Y') }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Jatuh Tempo</dt><dd class="font-medium text-slate-800">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Status</dt>
                        <dd><span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full @if($invoice->status === 'paid') bg-green-100 text-green-700 @elseif($invoice->status === 'cancelled') bg-red-100 text-red-700 @else bg-yellow-100 text-yellow-700 @endif">{{ $invoice->status === 'paid' ? 'Lunas' : ($invoice->status === 'cancelled' ? 'Dibatalkan' : 'Belum Bayar') }}</span></dd>
                    </div>
                    <div><dt class="text-xs text-slate-400">Nominal</dt><dd class="font-semibold text-lg text-slate-800">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Catatan</dt><dd class="text-slate-700 whitespace-pre-wrap">{{ $invoice->notes ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Dibuat oleh</dt><dd class="font-medium text-slate-800">{{ $invoice->creator?->name ?? '-' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Riwayat Pembayaran</h3>
                    @can('manage-admin')
                        @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                            <a href="{{ route('admin.payments.create', ['invoice_id' => $invoice->id]) }}"
                               class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-medium transition">
                                + Catat Pembayaran
                            </a>
                        @endif
                    @endcan
                </div>
                @forelse($invoice->payments as $payment)
                    <div class="border-b border-slate-100 py-3 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-500">{{ $payment->payment_date->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $payment->payment_method ?? '-' }}</p>
                            </div>
                            <div class="flex gap-2">
                                @if($payment->proof_file)
                                    <a href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs underline">Bukti</a>
                                @endif
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 text-xs">Detail</a>
                            </div>
                        </div>
                        @if($payment->notes)<p class="text-xs text-slate-400 mt-1">{{ $payment->notes }}</p>@endif
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Belum ada pembayaran.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
