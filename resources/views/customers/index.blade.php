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
            <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col sm:flex-row gap-3"
                  x-data="{ timer: null, loading: false }">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama customer..."
                    autocomplete="off"
                    class="w-full md:w-96 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    x-on:input.debounce.400ms="
                        loading = true;
                        clearTimeout(timer);
                        timer = setTimeout(() => {
                            fetch('{{ route('customers.index') }}?search=' + encodeURIComponent($el.value), {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            })
                            .then(r => r.text())
                            .then(html => {
                                document.getElementById('customer-table').innerHTML = html;
                                const url = $el.value ? '{{ url('customers') }}?search=' + encodeURIComponent($el.value) : '{{ url('customers') }}';
                                history.replaceState(null, '', url);
                                loading = false;
                            });
                        }, 100);
                    "
                >
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 font-medium transition whitespace-nowrap">
                    Cari
                </button>
            </form>
        </div>
        <div id="customer-table">
            @include('customers._list')
        </div>
            </div>
        </div>
    </div>

</x-app-layout>