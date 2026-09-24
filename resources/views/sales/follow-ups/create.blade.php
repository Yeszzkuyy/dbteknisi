<x-app-layout>
    <div >
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">{{ __('Tambah Follow Up') }}</h1>
                <p class="text-slate-500 mt-1">{{ __('Catat tindak lanjut dengan customer.') }}</p>
            </div>
            <a href="{{ route('sales.follow-ups.index') }}"
               class="px-4 py-2.5 rounded-xl bg-accent-500 text-white hover:bg-accent-600 dark:bg-accent-600 dark:hover:bg-accent-700 text-sm font-medium transition">
                {{ __('Kembali') }}
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('sales.follow-ups.store') }}" method="POST" data-ajax class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Customer <span class="text-red-500">*</span></label>
                    @if($customer)
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <div class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-700">{{ $customer->name }}</div>
                    @else
                        <select name="customer_id" id="customer_id" required
                                onchange="window.location.href='{{ route('sales.follow-ups.create') }}?customer_id='+this.value"
                                class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                            <option value="">{{ __('-- Pilih Customer --') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $customerId) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Terkait Meeting (opsional)') }}</label>
                    <select name="meeting_id"
                            class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('-- Tidak terkait meeting tertentu --') }}</option>
                        @foreach($meetings as $meeting)
                            <option value="{{ $meeting->id }}" {{ old('meeting_id', $meetingId) == $meeting->id ? 'selected' : '' }}>
                                Meeting {{ $meeting->meeting_date->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('meeting_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Deskripsi Follow Up') }} <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                              placeholder="{{ __('Jelaskan tindak lanjut yang dilakukan atau informasi tambahan dari user...') }}"
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tanggal Follow Up') }}</label>
                    <x-datepicker name="follow_up_date" value="{{ old('follow_up_date', date('Y-m-d')) }}"></x-datepicker>
                    @error('follow_up_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-700 text-white font-medium transition">
                        {{ __('Simpan') }}
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
