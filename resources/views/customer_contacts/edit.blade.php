<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Edit PIC
                </h1>
                <p class="text-slate-500 mt-1">
                    Edit PIC untuk customer: <span class="font-semibold text-slate-700">{{ $customer->name }}</span>
                </p>
            </div>
            <a href="{{ route('customers.show', $customer) }}" 
               class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                ← Kembali ke Customer
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 w-full">
            <form action="{{ route('customer-contacts.update', $customerContact) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $customerContact->name) }}" 
                               required
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Jabatan
                        </label>
                        <input type="text" 
                               name="position" 
                               value="{{ old('position', $customerContact->position) }}"
                               placeholder="Contoh: Manager, Teknisi, dll"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('position') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Phone
                        </label>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone', $customerContact->phone) }}"
                               placeholder="Nomor telepon PIC"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Email
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $customerContact->email) }}"
                               placeholder="Email PIC"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jadikan Primary --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" 
                               name="is_primary" 
                               id="is_primary" 
                               value="1" 
                               {{ old('is_primary', $customerContact->is_primary) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_primary" class="text-sm font-medium text-slate-700">
                            Jadikan PIC Utama (Primary)
                        </label>
                    </div>
                    @error('is_primary') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Update PIC
                    </button>
                    <a href="{{ route('customers.show', $customer) }}" 
                       class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>