<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Edit Meeting</h1>
                <p class="text-slate-500 mt-1">{{ __('Perbarui data meeting dengan customer.') }}</p>
            </div>
            <a href="{{ route('sales.meetings.index') }}"
               class="px-4 py-2.5 rounded-xl bg-accent-500 text-white hover:bg-accent-600 dark:bg-accent-600 dark:hover:bg-accent-700 text-sm font-medium transition">
                {{ __('Kembali') }}
            </a>
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

                    <div x-show="mode === 'new'" x-cloak>
                        <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Nama Customer / Perusahaan') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $meeting->customer->name ?? '') }}"
                               placeholder="{{ __('cth: PT Maju Bersama, CV Karya Abadi, dll...') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tanggal Meeting') }} <span class="text-red-500">*</span></label>
                    <x-datepicker name="meeting_date" required value="{{ old('meeting_date', $meeting->meeting_date->format('Y-m-d')) }}"></x-datepicker>
                </div>

                <div data-attendees>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Peserta') }}</label>
                    @php
                        $attendeeLines = array_pad(array_values(collect(preg_split('/[\r\n,;]+/', old('participants', $meeting->participants ?? '')))->map(fn ($n) => trim($n))->filter()->all()), 5, '');
                    @endphp
                    <div class="space-y-2" data-attendee-rows>
                        @for($i = 0; $i < 5; $i++)
                            <div class="flex items-center gap-3">
                                <span class="w-5 shrink-0 text-sm text-slate-500">{{ $i + 1 }}.</span>
                                <input type="text" data-attendee value="{{ $attendeeLines[$i] }}"
                                       placeholder="{{ __('Nama peserta...') }}"
                                       class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                            </div>
                        @endfor
                    </div>
                    <button type="button" data-attendees-add
                            class="mt-2 px-4 py-1.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-white text-sm font-medium transition">
                        {{ __('+ Tambah Peserta') }}
                    </button>
                    <input type="hidden" name="participants" data-attendees-value value="{{ old('participants', $meeting->participants) }}">
                    <script>
                        document.querySelectorAll('[data-attendees]').forEach(function (box) {
                            var rows = box.querySelector('[data-attendee-rows]');
                            box.querySelector('[data-attendees-add]').addEventListener('click', function () {
                                var n = rows.children.length + 1;
                                var row = document.createElement('div');
                                row.className = 'flex items-center gap-3';
                                row.innerHTML = '<span class="w-5 shrink-0 text-sm text-slate-500">' + n + '.</span>' +
                                    '<input type="text" data-attendee placeholder="{{ __('Nama peserta...') }}" class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">';
                                rows.appendChild(row);
                                row.querySelector('input').focus();
                            });
                            box.closest('form').addEventListener('submit', function () {
                                var names = Array.prototype.map.call(box.querySelectorAll('[data-attendee]'), function (el) { return el.value.trim(); }).filter(Boolean);
                                box.querySelector('[data-attendees-value]').value = names.join('\n');
                            });
                        });
                    </script>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Kebutuhan User') }}</label>
                    <textarea name="user_needs" rows="3"
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('user_needs', $meeting->user_needs) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Keluhan User') }}</label>
                    <textarea name="user_complaints" rows="3"
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('user_complaints', $meeting->user_complaints) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Sistem Existing') }}</label>
                    <textarea name="existing_system" rows="3"
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('existing_system', $meeting->existing_system) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Catatan Lain') }}</label>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $meeting->notes) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-700 text-white font-medium transition">
                        Update
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
