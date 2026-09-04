<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600 dark:text-blue-300">Data relasi</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100 sm:text-3xl">
                Daftar Customer
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola seluruh data customer Tridaya App.
            </p>
        </div>

        @can('manage-sales')
            <a href="{{ route('customers.create') }}"
               class="inline-flex items-center justify-center whitespace-nowrap rounded-xl bg-blue-600 px-5 py-2.5 font-medium text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 focus-visible:ring-offset-2 sm:self-start">
                <span class="mr-2 text-lg leading-none" aria-hidden="true">+</span>
                Tambah Customer
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="border-b border-slate-200/80 p-5 dark:border-slate-700 sm:p-6">
            <form method="GET" action="{{ route('customers.index') }}"
                  class="flex flex-col gap-3 sm:flex-row sm:items-start"
                  x-data="{ timer: null, loading: false, error: false }"
                  :aria-busy="loading">
                <div class="min-w-0 flex-1 sm:max-w-xl">
                    <label for="customer-search" class="sr-only">Cari customer</label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input id="customer-search"
                               type="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama, perusahaan, atau email..."
                               autocomplete="off"
                               class="h-11 w-full rounded-xl border-slate-300 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600"
                               x-on:input.debounce.400ms="
                                   loading = true;
                                   error = false;
                                   clearTimeout(timer);
                                   timer = setTimeout(() => {
                                       fetch('{{ route('customers.index') }}?search=' + encodeURIComponent($el.value.trim()), {
                                           headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                       })
                                       .then(response => {
                                           if (!response.ok) throw new Error('Search failed');
                                           return response.text();
                                       })
                                       .then(html => {
                                           document.getElementById('customer-table').innerHTML = html;
                                           const url = $el.value.trim() ? '{{ url('customers') }}?search=' + encodeURIComponent($el.value.trim()) : '{{ url('customers') }}';
                                           history.replaceState(null, '', url);
                                       })
                                       .catch(() => { error = true; })
                                       .finally(() => { loading = false; });
                                   }, 100);
                               "
                        >
                    </div>
                    <div class="min-h-5 pt-1.5 text-xs" aria-live="polite">
                        <span x-cloak x-show="loading" class="text-slate-400">Mencari customer...</span>
                        <span x-cloak x-show="error" class="text-red-500">Pencarian gagal. Coba lagi.</span>
                    </div>
                </div>
                <button type="submit"
                        class="h-11 rounded-xl bg-blue-600 px-5 font-medium text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 focus-visible:ring-offset-2">
                    Cari
                </button>
            </form>
        </div>

        <div id="customer-table">
            @include('customers._list')
        </div>
    </div>

</x-app-layout>
