<div class="overflow-x-auto">
    <table class="min-w-[760px] w-full">
        <thead class="bg-slate-50/80 dark:bg-slate-700/40">
            <tr>
                <th scope="col" class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-300 sm:px-6">Customer</th>
                <th scope="col" class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-300 sm:px-6">PIC</th>
                <th scope="col" class="px-5 py-4 text-center text-[11px] font-bold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-300 sm:px-6">Project</th>
                <th scope="col" class="px-5 py-4 text-right text-[11px] font-bold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-300 sm:px-6">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-200/70 dark:divide-slate-700/50">
            @forelse($customers as $customer)
                @php
                    $primaryContact = $customer->contacts->first();
                    $picName = $primaryContact?->name ?: $customer->contact_person;
                    $picPhone = $primaryContact?->phone ?: $customer->phone;
                @endphp
                <tr class="group transition-colors duration-200 hover:bg-slate-50 dark:hover:bg-white/5">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $customer->name }}</p>
                        <p class="mt-1 max-w-sm truncate text-sm text-slate-500 dark:text-slate-400">
                            {{ $customer->address ?: 'Alamat belum diisi' }}
                        </p>
                    </td>

                    <td class="px-5 py-4 sm:px-6">
                        @if($picName)
                            <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $picName }}</p>
                            @if($picPhone)
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $picPhone }}</p>
                            @endif
                        @else
                            <x-status-badge color="yellow">Belum ada PIC</x-status-badge>
                        @endif
                    </td>

                    <td class="px-5 py-4 text-center sm:px-6">
                        @if($customer->projects_count > 0)
                            <span class="inline-flex min-h-8 min-w-8 items-center justify-center rounded-full bg-blue-100 px-2.5 text-sm font-bold tabular-nums text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                                {{ $customer->projects_count }}
                            </span>
                        @else
                            <span class="text-sm font-semibold tabular-nums text-slate-400 dark:text-slate-500">0</span>
                        @endif
                    </td>

                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('customers.show', $customer) }}"
                               title="Lihat customer"
                               aria-label="Lihat customer {{ $customer->name }}"
                               class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 transition hover:bg-indigo-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20">
                                <x-icon name="eye" class="h-4 w-4" />
                            </a>

                            @can('manage-sales')
                                <a href="{{ route('customers.edit', $customer) }}"
                                   title="Edit customer"
                                   aria-label="Edit customer {{ $customer->name }}"
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700 transition hover:bg-blue-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 dark:bg-blue-500/10 dark:text-blue-300 dark:hover:bg-blue-500/20">
                                    <x-icon name="edit" class="h-4 w-4" />
                                </a>
                            @endcan

                            @can('manage-sales')
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus customer ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Hapus customer"
                                            aria-label="Hapus customer {{ $customer->name }}"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-700 transition hover:bg-red-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <div class="mx-auto flex max-w-sm flex-col items-center">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-300">
                                <x-icon name="users" class="h-6 w-6" />
                            </span>
                            @if(request()->filled('search'))
                                <p class="mt-4 text-sm font-semibold text-slate-700 dark:text-slate-200">Customer tidak ditemukan</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Coba gunakan nama, perusahaan, atau email lain.</p>
                            @else
                                <p class="mt-4 text-sm font-semibold text-slate-700 dark:text-slate-200">Belum ada customer</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Data customer yang baru ditambahkan akan muncul di sini.</p>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
