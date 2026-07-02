<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <a href="{{ route('users.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                    Tambah User
                </a>

                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="{{ $user->trashed() ? 'bg-gray-50 opacity-60' : '' }}">
                            <td class="px-4 py-2">{{ $user->id }}</td>
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">{{ $user->role->label() }}</td>
                            <td class="px-4 py-2">
                                @if($user->trashed())
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Nonaktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                @if($user->trashed())
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline">Aktifkan</button>
                                    </form>
                                @else
                                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:underline">Edit</a>
                                    @if($user->id !== 1)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Nonaktifkan user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Nonaktifkan</button>
                                    </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>