<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Admin Panel</h1>
            <p class="text-slate-500 mt-1">Manajemen User, Role & Permission (Super Admin)</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin-panel.roles.create') }}"
               class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition">
                + Tambah Role
            </a>
            <a href="{{ route('admin-panel.users.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                + Tambah User
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b border-slate-200 mb-6">
        <button class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-blue-600 text-blue-600" data-tab="users">Users</button>
        <button class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-transparent text-slate-400 hover:text-slate-600" data-tab="roles">Roles & Permissions</button>
        <button class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-transparent text-slate-400 hover:text-slate-600" data-tab="audit">Audit Log</button>
    </div>

    {{-- Users Tab --}}
    <div id="tab-users" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Dibuat</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ $user->name }}</p>
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-blue-600">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @forelse($user->roles as $role)
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-slate-400 text-sm">-</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                    {{ $user->trashed() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $user->trashed() ? 'Nonaktif' : 'Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin-panel.users.edit', $user) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-3">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin-panel.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">Tidak ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Roles Tab --}}
    <div id="tab-roles" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800">Daftar Role & Permissions</h3>
            </div>
            <div class="divide-y divide-slate-200">
                @foreach($roles as $role)
                    <div class="p-4 hover:bg-slate-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-slate-900">{{ $role->name }}</h4>
                                <p class="text-sm text-slate-500">{{ $role->permissions->count() }} permissions</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($role->name !== 'super-admin')
                                    <a href="{{ route('admin-panel.roles.edit', $role) }}"
                                       class="px-3 py-1.5 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">Edit</a>
                                    <form action="{{ route('admin-panel.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Hapus role ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">Hapus</button>
                                    </form>
                                @else
                                    <span class="px-3 py-1.5 text-sm text-slate-400">Protected</span>
                                @endif
                            </div>
                        </div>
                        @if($role->permissions->count())
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach($role->permissions as $perm)
                                    <span class="px-2 py-0.5 text-xs rounded bg-slate-100 text-slate-600">{{ $perm->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Audit Log Tab --}}
    <div id="tab-audit" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Audit Log</h3>
            </div>
            <div class="text-center py-12 text-slate-500">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-lg font-medium">Audit Log belum tersedia</p>
                <p class="mt-1">Membutuhkan model AuditLog untuk pencatatan aktivitas user</p>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            
            tabBtns.forEach(b => {
                b.classList.remove('border-blue-600', 'text-blue-600');
                b.classList.add('border-transparent', 'text-slate-400');
            });
            btn.classList.remove('border-transparent', 'text-slate-400');
            btn.classList.add('border-blue-600', 'text-blue-600');
            
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById('tab-' + tab).classList.remove('hidden');
        });
    });
});
</script>