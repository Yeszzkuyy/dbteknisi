<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

    <div class="flex justify-between items-center">

        <div>

            <p class="text-sm text-slate-500">
                {{ $title }}
            </p>

            <h2 class="text-3xl font-bold mt-2 text-slate-800">
                {{ $value }}
            </h2>

        </div>

        <div class="text-4xl">
            {!! $icon ?? '' !!}
        </div>

    </div>

</div>