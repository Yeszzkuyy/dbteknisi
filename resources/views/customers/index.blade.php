<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Client / Customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <a href="{{ route('customers.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                    + Tambah Client
                </a>

                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Perusahaan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">PIC Utama</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Project</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($customers as $customer)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $customer->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ Str::limit($customer->address, 40) }}</td>
                            <td class="px-4 py-2">
                                @if($customer->contacts->isNotEmpty())
                                    <span class="font-semibold text-gray-800">{{ $customer->contacts->first()->name }}</span>
                                    <br><span class="text-xs text-gray-500">{{ $customer->contacts->first()->phone }}</span>
                                @else
                                    <span class="text-xs text-red-500 italic">Belum ada PIC</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-semibold">
                                    {{ $customer->projects_count }} Project
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2 text-sm">
                                <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:underline font-medium">Detail</a>
                                <a href="{{ route('customers.edit', $customer) }}" class="text-yellow-600 hover:underline">Edit</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus client ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada data client.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>