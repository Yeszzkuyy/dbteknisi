<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Client: {{ $customer->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border-l-4 border-indigo-500">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Informasi Perusahaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Telepon Kantor</p>
                        <p class="font-medium">{{ $customer->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email Utama</p>
                        <p class="font-medium">{{ $customer->email ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Alamat Lengkap</p>
                        <p class="font-medium">{{ $customer->address ?? '-' }}</p>
                    </div>
                    @if($customer->notes)
                    <div class="md:col-span-2 mt-2 p-3 bg-gray-50 rounded text-sm text-gray-700">
                        <strong>Catatan:</strong> {{ $customer->notes }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Project</h3>
                    </div>

                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Project</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Pada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customer->projects as $project)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $project->name ?? 'Project ID: ' . $project->id }}</td>
                            <td class="px-4 py-2 text-sm">{{ $project->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500 text-sm">Belum ada project untuk client ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar PIC / Customer Contacts</h3>
                    <a href="{{ route('customer-contacts.create', $customer) }}" class="px-4 py-2 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700">
                        + Tambah PIC
                    </a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone & Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customer->contacts as $contact)
                        <tr class="{{ $contact->is_primary ? 'bg-indigo-50' : '' }}">
                            <td class="px-4 py-2 font-medium">{{ $contact->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ $contact->position ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ $contact->phone ?? '-' }} <br>
                                <span class="text-xs text-gray-400">{{ $contact->email ?? '' }}</span>
                            </td>
                            <td class="px-4 py-2">
                                @if($contact->is_primary)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-bold">★ Primary PIC</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500 text-sm">Belum ada data PIC yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>