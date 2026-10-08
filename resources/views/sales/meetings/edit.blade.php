<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Edit Meeting</h1>
                <p class="text-slate-500 mt-1">{{ __('Perbarui data meeting dengan customer.') }}</p>
            </div>
            <x-icon-button as="a" icon="back" href="{{ route('sales.follow-ups.index') }}" title="Back" />
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('sales.meetings.update', $meeting) }}" method="POST" data-ajax class="space-y-6">
                @csrf @method('PUT')

                <div x-data="{ mode: '{{ old('customer_mode', 'existing') }}' }">
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
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $meeting->customer->name ?? '') }}"
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
                                    <option value="{{ $pt }}" @selected(old('pt_group', $meeting->customer->pt_group ?? null) === $pt)>{{ $pt }}</option>
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
                            :selected="old('customer_id', $meeting->customer_id)"
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
                            <option value="{{ $lead->id }}" @selected(old('lead_id', $meeting->lead_id) == $lead->id)>
                                {{ 'Lead #'.$lead->id }} — {{ ucfirst($lead->status) }}{{ $lead->incoming_date ? ' — '.$lead->incoming_date->format('d M Y') : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('lead_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tanggal Meeting') }} <span class="text-red-500">*</span></label>
                    <x-datepicker name="meeting_date" required value="{{ old('meeting_date', $meeting->meeting_date->format('Y-m-d')) }}"></x-datepicker>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meeting Notes') }} <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="4" required
                              placeholder="{{ __('Write a brief meeting note...') }}"
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $meeting->notes) }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="group relative overflow-hidden px-6 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-500 text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-accent-500/40 hover:brightness-110 active:scale-95">
                        <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-full"></span>
                        {{ __('Save') }}
                    </button>
                    <a href="{{ route('sales.follow-ups.index') }}"
                       class="group relative overflow-hidden px-6 py-2.5 rounded-xl bg-accent-500 hover:bg-accent-600 text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg active:scale-95">
                        <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-full"></span>
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
