<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah PIC — {{ $customer->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('customer-contacts.store', $customer) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" name="position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_primary" id="is_primary" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                        <label for="is_primary" class="ml-2 text-sm text-gray-700">Jadikan PIC Utama (Primary)</label>
                    </div>

                    <div class="flex items-center gap-3 pt-2">

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Simpan
                        </button>
                        <a href="{{ route('customers.show', $customer) }}" class="text-sm text-gray-600 hover:underline">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>