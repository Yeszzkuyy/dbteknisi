@props([
    'name' => 'id',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Cari & pilih...',
])

@php
    $selectedLabel = ($selected !== null && isset($options[$selected])) ? $options[$selected] : null;
@endphp

<div
    x-data="{
        options: {{ json_encode($options, JSON_HEX_APOS | JSON_HEX_QUOT) }},
        selectedId: {{ $selected !== null ? json_encode($selected) : 'null' }},
        selectedLabel: {{ $selectedLabel !== null ? json_encode($selectedLabel, JSON_HEX_APOS | JSON_HEX_QUOT) : 'null' }},
        query: {{ $selectedLabel !== null ? json_encode($selectedLabel, JSON_HEX_APOS | JSON_HEX_QUOT) : '""' }},
        open: false,
        get filtered() {
            const q = this.query.toLowerCase();
            return Object.entries(this.options).filter(([id, label]) => !q || label.toLowerCase().includes(q));
        },
        select(id, label) {
            this.selectedId = id;
            this.selectedLabel = label;
            this.query = label;
            this.open = false;
        }
    }"
    class="relative"
>
    <input type="hidden" name="{{ $name }}" :value="selectedId">
    <input
        type="text"
        x-model="query"
        @focus="open = true"
        @input="if (query !== selectedLabel) selectedId = null; open = true"
        @keydown.escape="open = false"
        @blur="setTimeout(() => open = false, 150)"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
    >
    <div x-show="open" x-cloak x-transition
         class="absolute z-20 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-lg max-h-60 overflow-y-auto">
        <template x-for="[id, label] in filtered" :key="id">
            <button type="button"
                    @mousedown.prevent="select(id, label)"
                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition"
                    :class="String(id) === String(selectedId) ? 'bg-blue-50 dark:bg-slate-700' : ''"
                    x-text="label"></button>
        </template>
        <p x-show="filtered.length === 0" class="px-4 py-3 text-sm text-slate-400">Tidak ada hasil.</p>
    </div>
</div>