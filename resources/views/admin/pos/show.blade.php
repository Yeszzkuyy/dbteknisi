<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-3xl font-bold text-slate-800">{{ $purchaseOrder->po_number }}</h1><p class="text-slate-500 mt-1">{{ $purchaseOrder->customer->name }}</p></div>
            <div class="flex gap-2">
                @can('manage-admin')<a href="{{ route('admin.pos.edit', $purchaseOrder) }}" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium transition">Edit</a>@endcan
                <a href="{{ route('admin.pos.index') }}" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">Kembali</a>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><dt class="text-xs text-slate-400">No PO</dt><dd class="font-mono font-semibold text-slate-800">{{ $purchaseOrder->po_number }}</dd></div>
                <div><dt class="text-xs text-slate-400">Customer</dt><dd class="font-medium text-slate-800">{{ $purchaseOrder->customer->name }}</dd></div>
                <div><dt class="text-xs text-slate-400">Proyek</dt><dd class="font-medium text-slate-800">{{ $purchaseOrder->project?->project_name ?? '-' }}</dd></div>
                <div><dt class="text-xs text-slate-400">Tanggal Terbit</dt><dd class="font-medium text-slate-800">{{ $purchaseOrder->issue_date->format('d M Y') }}</dd></div>
                <div><dt class="text-xs text-slate-400">Nominal</dt><dd class="font-semibold text-lg text-slate-800">Rp {{ number_format($purchaseOrder->amount, 0, ',', '.') }}</dd></div>
                <div><dt class="text-xs text-slate-400">Status</dt>
                    <dd><span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                        @if($purchaseOrder->status === 'selesai') bg-green-100 text-green-700
                        @elseif($purchaseOrder->status === 'dibatalkan') bg-red-100 text-red-700
                        @elseif($purchaseOrder->status === 'diproses') bg-yellow-100 text-yellow-700
                        @else bg-slate-100 text-slate-600 @endif">{{ ucfirst($purchaseOrder->status) }}</span></dd>
                </div>
            </dl>
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Item</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $purchaseOrder->items }}</p>
            </div>
            @if($purchaseOrder->notes)
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Catatan</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $purchaseOrder->notes }}</p>
            </div>
            @endif
            <div class="mt-4 text-xs text-slate-400">Dibuat oleh: {{ $purchaseOrder->creator?->name ?? '-' }}</div>
        </div>
    </div>
</x-app-layout>
