<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-3xl font-bold text-slate-800">Detail Pembayaran</h1><p class="text-slate-500 mt-1">{{ $payment->invoice->invoice_number }}</p></div>
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">Kembali</a>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><dt class="text-xs text-slate-400">Invoice</dt><dd class="font-mono font-semibold text-slate-800">{{ $payment->invoice->invoice_number }}</dd></div>
                <div><dt class="text-xs text-slate-400">Customer</dt><dd class="font-medium text-slate-800">{{ $payment->invoice->customer->name }}</dd></div>
                <div><dt class="text-xs text-slate-400">Tanggal Bayar</dt><dd class="font-medium text-slate-800">{{ $payment->payment_date->format('d M Y') }}</dd></div>
                <div><dt class="text-xs text-slate-400">Metode</dt><dd class="font-medium text-slate-800">{{ $payment->payment_method ?? '-' }}</dd></div>
                <div><dt class="text-xs text-slate-400">Nominal</dt><dd class="font-semibold text-lg text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd></div>
                <div><dt class="text-xs text-slate-400">Dibuat oleh</dt><dd class="font-medium text-slate-800">{{ $payment->creator?->name ?? '-' }}</dd></div>
            </dl>
            @if($payment->proof_file)
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Bukti Transfer</h3>
                <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Lihat Bukti Transfer
                </a>
            </div>
            @endif
            @if($payment->notes)
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Catatan</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
