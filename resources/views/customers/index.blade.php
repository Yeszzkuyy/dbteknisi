<x-app-layout>

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Daftar Customer
            </h1>

            <p class="text-slate-500 mt-1">
                Kelola seluruh data customer Tridaya App.
            </p>
        </div>

        {{-- GANTI: Cek permission 'create-clients' atau 'create-customers' --}}
        @can('manage-sales')
            <a href="{{ route('customers.create') }}"
               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                + Tambah Customer
            </a>
        @endcan

    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600">

        <div class="p-5 border-b">
            <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama customer..."
                    class="w-full md:w-96 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                >
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 font-medium transition whitespace-nowrap">
                    Cari
                </button>
            </form>
        </div>
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

                            <tr class="hover:bg-slate-50 transition">

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $customer->name }}
                                    </div>

                                    <div class="text-sm text-slate-500 mt-1">
                                        {{ $customer->address }}
                                    </div>

                                </td>

                                <td class="px-6 py-4">

                                    @if($customer->contacts->isNotEmpty())

                                        <div class="font-medium dark:text-slate-100">
                                            {{ $customer->contacts->first()->name }}
                                        </div>

                                        <div class="text-sm text-slate-500">
                                            {{ $customer->contacts->first()->phone }}
                                        </div>

                                    @else

                                        <span class="text-slate-400 italic">
                                            Belum ada PIC
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">

                                        {{ $customer->projects_count }}

                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-3">

                                        <a href="{{ route('customers.show',$customer) }}"
                                           class="text-blue-600 hover:text-blue-800">
                                            Detail
                                        </a>

                                        {{-- GANTI: Cek permission 'edit-customers' --}}
                                        @can('manage-sales')
                                            <a href="{{ route('customers.edit',$customer) }}"
                                               class="text-amber-600 hover:text-amber-800">
                                                Edit
                                            </a>
                                        @endcan

                                        {{-- GANTI: Cek permission 'delete-customers' --}}
                                        @can('manage-sales')
                                            <form
                                                action="{{ route('customers.destroy',$customer) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus customer ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    class="text-red-600 hover:text-red-800"
                                                >
                                                    Hapus
                                                </button>

                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center text-slate-400">

                                    Belum ada customer.

                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-app-layout>