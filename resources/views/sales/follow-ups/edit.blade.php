<x-app-layout>
    <div >
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Edit Follow Up</h1>
                <p class="text-slate-500 mt-1">{{ __('Perbarui follow up customer.') }}</p>
            </div>
            <a href="{{ route('sales.follow-ups.index') }}"
               class="px-4 py-2.5 rounded-xl bg-accent-500 text-white hover:bg-accent-600 dark:bg-accent-600 dark:hover:bg-accent-700 text-sm font-medium transition">
                {{ __('Kembali') }}
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('sales.follow-ups.update', $followUp) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" required
                            class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('-- Pilih Customer --') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $followUp->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Terkait Meeting') }}</label>
                    <select name="meeting_id"
                            class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('-- Tidak terkait meeting tertentu --') }}</option>
                        @foreach($meetings as $meeting)
                            <option value="{{ $meeting->id }}" {{ old('meeting_id', $followUp->meeting_id) == $meeting->id ? 'selected' : '' }}>
                                Meeting {{ $meeting->meeting_date->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Deskripsi') }} <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('description', $followUp->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tanggal Follow Up') }}</label>
                    <x-datepicker name="follow_up_date" value="{{ old('follow_up_date', $followUp->follow_up_date?->format('Y-m-d')) }}"></x-datepicker>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-700 text-white font-medium transition">
                        Update
                    </button>
                    <a href="{{ route('sales.follow-ups.index') }}"
                       class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
