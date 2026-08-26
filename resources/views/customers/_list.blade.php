        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider whitespace-nowrap">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider whitespace-nowrap">PIC</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider whitespace-nowrap">Project</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-600">

                        @forelse($customers as $customer)

                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $customer->name }}
                                    </div>

                                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                        {{ $customer->address }}
                                    </div>

                                </td>

                                <td class="px-6 py-4">

                                    @if($customer->contacts->isNotEmpty())

                                        <div class="font-medium dark:text-slate-100">
                                            {{ $customer->contacts->first()->name }}
                                        </div>

                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $customer->contacts->first()->phone }}
                                        </div>

                                    @else

                                        <span class="text-slate-400 dark:text-slate-500 italic">
                                            Belum ada PIC
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 text-sm font-semibold">

                                        {{ $customer->projects_count }}

                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">

                                        <a href="{{ route('customers.show',$customer) }}" title="Detail customer"
                                           class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </a>

                                        {{-- GANTI: Cek permission 'edit-customers' --}}
                                        @can('manage-sales')
                                            <a href="{{ route('customers.edit',$customer) }}" title="Edit customer"
                                               class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </a>
                                        @endcan

                                        {{-- GANTI: Cek permission 'delete-customers' --}}
                                        @can('manage-sales')
                                            <form
                                                action="{{ route('customers.destroy',$customer) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus customer ini?')"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button title="Hapus customer"
                                                    class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>

                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center text-slate-400 dark:text-slate-500">

                                    Belum ada customer.

                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

