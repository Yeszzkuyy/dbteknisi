<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Edit Lead: {{ $lead->customer->name }}</h1>
            <p class="text-slate-500 mt-1">Perbarui informasi lead / opportunity</p>
        </div>
        <a href="{{ route('leads.show', $lead) }}"
           class="px-4 py-2.5 rounded-xl bg-blue-400 hover:bg-blue-500 text-white text-sm font-medium transition">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('leads.update', $lead) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div x-data="{ mode: '{{ old('customer_mode', $lead->customer_id ? 'existing' : 'new') }}' }">
                <label class="block text-sm font-medium text-slate-700 mb-2">Customer <span class="text-red-500">*</span></label>

                <div class="flex items-center gap-6 mb-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                        <input type="radio" name="customer_mode" value="new" x-model="mode" class="accent-blue-600">
                        Customer Baru (ketik nama)
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                        <input type="radio" name="customer_mode" value="existing" x-model="mode" class="accent-blue-600">
                        Pilih Customer Lama
                    </label>
                </div>

                <div x-show="mode === 'new'" x-cloak>
                    <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Customer / Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $lead->customer->name ?? '') }}"
                           placeholder="cth: PT Maju Bersama, CV Karya Abadi, dll..."
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="mode === 'existing'" x-cloak>
                    <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Pilih Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" id="customer_id"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $lead->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->company ?? 'Perusahaan' }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ $lead->status == 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="proposal" {{ $lead->status == 'proposal' ? 'selected' : '' }}>Proposal</option>
                        <option value="won" {{ $lead->status == 'won' ? 'selected' : '' }}>Won</option>
                        <option value="lost" {{ $lead->status == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>

                <div>
                    <label for="source" class="block text-sm font-medium text-slate-700 mb-1">Sumber Lead</label>
                    <select name="source" id="source"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Sumber</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ $lead->source == $source ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $source)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="opportunity_value" class="block text-sm font-medium text-slate-700 mb-1">Nilai Opportunity (Rp)</label>
                    <input type="number" name="opportunity_value" id="opportunity_value" value="{{ $lead->opportunity_value }}"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                           step="0.01" min="0">
                </div>

                <div>
                    <label for="expected_close_date" class="block text-sm font-medium text-slate-700 mb-1">Target Tanggal Close</label>
                    <input type="date" name="expected_close_date" id="expected_close_date" value="{{ $lead->expected_close_date?->format('Y-m-d') }}"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-700 mb-1">PIC Marketing</label>
                <select name="assigned_to" id="assigned_to"
                        class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih PIC</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $lead->assigned_to == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                <textarea name="notes" id="notes" rows="4"
                          class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ $lead->notes }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('leads.show', $lead) }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    Update Lead
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
