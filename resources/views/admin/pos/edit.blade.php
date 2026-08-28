<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div><h1 class="text-3xl font-bold text-slate-800">Edit PO</h1><p class="text-slate-500 mt-1">{{ $purchaseOrder->po_number }}</p></div>
            <a href="{{ route('admin.pos.index') }}" class="px-4 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition">Kembali</a>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('admin.pos.update', $purchaseOrder) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Customer <span class="text-red-500">*</span></label>
                    <x-searchable-select
                        name="customer_id"
                        :options="$customers->mapWithKeys(fn ($c) => [$c->id => $c->name])->all()"
                        :selected="old('customer_id', $purchaseOrder->customer_id)"
                        placeholder="Cari & pilih customer..."
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Proyek Terkait</label>
                    <select name="project_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Tidak terkait proyek --</option>
                        @foreach($projects as $p) <option value="{{ $p->id }}" {{ old('project_id', $purchaseOrder->project_id) == $p->id ? 'selected' : '' }}>{{ $p->project_name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Item <span class="text-red-500">*</span></label>
                    <textarea name="items" rows="4" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('items', $purchaseOrder->items) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount', $purchaseOrder->amount) }}" required step="0.01" min="0" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            @foreach($statuses as $val => $label) <option value="{{ $val }}" {{ old('status', $purchaseOrder->status) == $val ? 'selected' : '' }}>{{ $label }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Terbit <span class="text-red-500">*</span></label>
                    <x-datepicker name="issue_date" required value="{{ old('issue_date', $purchaseOrder->issue_date->format('Y-m-d')) }}"></x-datepicker>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">Update</button>
                    <a href="{{ route('admin.pos.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
