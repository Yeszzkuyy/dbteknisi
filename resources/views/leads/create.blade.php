<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Tambah Lead / Opportunity Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('leads.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-slate-700">Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" id="customer_id" required
                                class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }} ({{ $customer->company ?? 'Perusahaan' }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="new">New</option>
                                <option value="contacted">Contacted</option>
                                <option value="qualified">Qualified</option>
                                <option value="proposal">Proposal</option>
                                <option value="won">Won</option>
                                <option value="lost">Lost</option>
                            </select>
                        </div>

                        <div>
                            <label for="source" class="block text-sm font-medium text-slate-700">Sumber Lead</label>
                            <select name="source" id="source"
                                    class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Sumber</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source }}">{{ ucfirst(str_replace('_', ' ', $source)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="opportunity_value" class="block text-sm font-medium text-slate-700">Nilai Opportunity (Rp)</label>
                            <input type="number" name="opportunity_value" id="opportunity_value"
                                   class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   step="0.01" min="0">
                        </div>

                        <div>
                            <label for="expected_close_date" class="block text-sm font-medium text-slate-700">Target Tanggal Close</label>
                            <input type="date" name="expected_close_date" id="expected_close_date"
                                   class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-slate-700">PIC Marketing</label>
                        <select name="assigned_to" id="assigned_to"
                                class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih PIC</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700">Catatan</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('leads.index') }}"
                           class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                            Simpan Lead
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>