<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">{{ __('Log Meeting') }}</h1>
                <p class="text-slate-500 mt-1">{{ __('Catat hasil meeting dengan customer.') }}</p>
            </div>
            <a href="{{ route('sales.meetings.index') }}"
               class="px-4 py-2.5 rounded-xl bg-accent-500 text-white hover:bg-accent-600 dark:bg-accent-600 dark:hover:bg-accent-700 text-sm font-medium transition">
                {{ __('Kembali') }}
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('sales.meetings.store') }}" method="POST" data-ajax class="space-y-6">
                @csrf

                <div x-data="{ mode: '{{ old('customer_mode', $preselectedCustomerId ? 'existing' : 'new') }}' }">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Customer <span class="text-red-500">*</span></label>

                    <div class="flex items-center gap-6 mb-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                            <input type="radio" name="customer_mode" value="new" x-model="mode" class="accent-blue-600">
                            {{ __('Customer Baru (ketik nama)') }}
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                            <input type="radio" name="customer_mode" value="existing" x-model="mode" class="accent-blue-600">
                            {{ __('Pilih Customer Lama') }}
                        </label>
                    </div>

                    <div x-show="mode === 'new'" x-cloak class="space-y-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Nama Customer / Perusahaan') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                                   placeholder="{{ __('cth: PT Maju Bersama, CV Karya Abadi, dll...') }}"
                                   class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                            @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="pt_group" class="block text-sm font-medium text-slate-700 mb-1">{{ __('PT / Company') }} <span class="text-red-500">*</span></label>
                            <select name="pt_group" id="pt_group"
                                    class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                                <option value="">{{ __('-- Pilih PT --') }}</option>
                                @foreach($ptGroups ?? [] as $pt)
                                    <option value="{{ $pt }}" @selected(old('pt_group') === $pt)>{{ $pt }}</option>
                                @endforeach
                            </select>
                            @error('pt_group') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div x-show="mode === 'existing'" x-cloak>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Pilih Customer') }} <span class="text-red-500">*</span></label>
                        <x-searchable-select
                            name="customer_id"
                            :options="$customers->mapWithKeys(fn ($c) => [$c->id => $c->name])->all()"
                            :selected="old('customer_id', $preselectedCustomerId)"
                            placeholder="{{ __('Ketik nama customer untuk mencari...') }}"
                        />
                        @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Terkait Lead (opsional)') }}</label>
                    <select name="lead_id"
                            class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('-- Tidak terkait lead tertentu --') }}</option>
                        @foreach($leads ?? [] as $lead)
                            <option value="{{ $lead->id }}" @selected(old('lead_id', $preselectedLeadId ?? null) == $lead->id)>
                                {{ $lead->customer->name ?? 'Lead #'.$lead->id }} — {{ ucfirst($lead->status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('lead_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tanggal Meeting') }} <span class="text-red-500">*</span></label>
                    <x-datepicker name="meeting_date" required value="{{ old('meeting_date', date('Y-m-d')) }}"></x-datepicker>
                    @error('meeting_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meeting Notes') }} <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="4" required
                              placeholder="{{ __('Write a brief meeting note...') }}"
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-700 text-white font-medium transition">
                        {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('sales.meetings.index') }}"
                       class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
