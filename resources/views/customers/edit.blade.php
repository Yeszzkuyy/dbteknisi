<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                    Edit Customer
                </h1>
                <p class="text-slate-500 mt-1">
                    {{ __('Edit data customer:') }} <span class="font-semibold text-slate-700">{{ $customer->name }}</span>
                </p>
            </div>
            <a href="{{ route('customers.show', $customer) }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-accent-500 hover:bg-accent-600 text-white text-sm font-medium transition sm:self-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Kembali ke Customer') }}
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 w-full">
            <form action="{{ route('customers.update', $customer) }}" method="POST" data-loading-text="{{ __('Menyimpan…') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Nama Perusahaan --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            {{ __('Nama Perusahaan') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $customer->name) }}"
                               required
                               class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Company Group --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            {{ __('Customer dari Company') }} <span class="text-red-500">*</span>
                        </label>
                        <x-glide-select name="pt_group" :label="__('Customer dari Company')" required
                            :options="collect($ptGroups)->all()"
                            :value="old('pt_group', $customer->pt_group ?? '')"
                            :placeholder="__('Pilih Company')" :error="$errors->first('pt_group')" />
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Address
                        </label>
                        <textarea name="address" 
                                  rows="3"
                                  class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('address', $customer->address) }}</textarea>
                        @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            {{ __('No Telp') }}
                        </label>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone', $customer->phone) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- WhatsApp --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            {{ __('No WA') }}
                        </label>
                        <input type="text"
                               name="whatsapp"
                               value="{{ old('whatsapp', $customer->whatsapp) }}"
                               inputmode="tel"
                               class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        @error('whatsapp') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Email
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $customer->email) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Notes
                        </label>
                        <textarea name="notes" 
                                  rows="3"
                                  class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $customer->notes) }}</textarea>
                        @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse sm:flex-row gap-3">
                    <a href="{{ route('customers.show', $customer) }}" 
                       class="px-6 py-3 rounded-xl bg-accent-500 hover:bg-accent-600 text-white font-medium transition text-base text-center">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-accent-600 hover:bg-accent-700 text-white font-medium transition text-base">
                        {{ __('Update Customer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>