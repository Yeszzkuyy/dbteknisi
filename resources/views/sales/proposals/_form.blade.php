<div class="space-y-4">
    <input type="hidden" name="lead_id" value="{{ $lead->id ?? $proposal->lead_id }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('Tanggal Proposal') }} <span class="text-red-500">*</span></label>
            <input type="date" name="proposal_date" required value="{{ old('proposal_date', isset($proposal) ? $proposal->proposal_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                   class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('Berlaku Sampai') }}</label>
            <input type="date" name="valid_until" value="{{ old('valid_until', isset($proposal) ? $proposal->valid_until?->format('Y-m-d') : '') }}"
                   class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
            @error('valid_until') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Item') }} <span class="text-red-500">*</span></label>
            <button type="button" onclick="proposalAddRow()"
                    class="px-3 py-1.5 text-xs rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium transition">{{ __('+ Item') }}</button>
        </div>
        <div id="proposal-items" class="space-y-2">
            @php $rows = old('items', isset($proposal) ? $proposal->items->map(fn ($i) => ['description' => $i->description, 'quantity' => $i->quantity, 'unit' => $i->unit, 'unit_price' => $i->unit_price, 'discount' => $i->discount])->all() : [['description' => '', 'quantity' => 1, 'unit' => 'pcs', 'unit_price' => 0, 'discount' => 0]]); @endphp
            @foreach($rows as $i => $row)
                <div class="proposal-row grid grid-cols-12 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                    <input type="text" name="items[{{ $i }}][description]" required value="{{ $row['description'] }}" placeholder="{{ __('Deskripsi') }}"
                           class="col-span-12 md:col-span-5 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
                    <input type="number" name="items[{{ $i }}][quantity]" required min="0.01" step="0.01" value="{{ $row['quantity'] }}" placeholder="{{ __('Qty') }}"
                           class="col-span-4 md:col-span-2 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
                    <input type="text" name="items[{{ $i }}][unit]" value="{{ $row['unit'] }}" placeholder="{{ __('Satuan') }}"
                           class="col-span-4 md:col-span-1 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
                    <input type="number" name="items[{{ $i }}][unit_price]" required min="0" step="0.01" value="{{ $row['unit_price'] }}" placeholder="{{ __('Harga') }}"
                           class="col-span-4 md:col-span-2 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
                    <input type="number" name="items[{{ $i }}][discount]" min="0" step="0.01" value="{{ $row['discount'] }}" placeholder="{{ __('Diskon') }}"
                           class="col-span-10 md:col-span-1 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
                    <button type="button" onclick="this.closest('.proposal-row').remove()"
                            class="col-span-2 md:col-span-1 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium transition">×</button>
                </div>
            @endforeach
        </div>
        @error('items') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('Diskon Total (Rp)') }}</label>
            <input type="number" name="discount" min="0" step="0.01" value="{{ old('discount', $proposal->discount ?? 0) }}"
                   class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('Pajak (Rp)') }}</label>
            <input type="number" name="tax" min="0" step="0.01" value="{{ old('tax', $proposal->tax ?? 0) }}"
                   class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('Catatan') }}</label>
        <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">{{ old('notes', $proposal->notes ?? '') }}</textarea>
    </div>
</div>

<script>
let proposalRowIdx = {{ count($rows) }};
function proposalAddRow() {
    const wrap = document.getElementById('proposal-items');
    const div = document.createElement('div');
    div.className = 'proposal-row grid grid-cols-12 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50';
    const i = proposalRowIdx++;
    div.innerHTML =
        '<input type="text" name="items[' + i + '][description]" required placeholder="{{ __('Deskripsi') }}" class="col-span-12 md:col-span-5 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">' +
        '<input type="number" name="items[' + i + '][quantity]" required min="0.01" step="0.01" value="1" class="col-span-4 md:col-span-2 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">' +
        '<input type="text" name="items[' + i + '][unit]" value="pcs" class="col-span-4 md:col-span-1 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">' +
        '<input type="number" name="items[' + i + '][unit_price]" required min="0" step="0.01" value="0" class="col-span-4 md:col-span-2 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">' +
        '<input type="number" name="items[' + i + '][discount]" min="0" step="0.01" value="0" class="col-span-10 md:col-span-1 rounded-lg border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">' +
        '<button type="button" onclick="this.closest(\'.proposal-row\').remove()" class="col-span-2 md:col-span-1 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium transition">×</button>';
    wrap.appendChild(div);
}
</script>
