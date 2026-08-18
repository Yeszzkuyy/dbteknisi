@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition w-full sm:w-auto']) }}>
    {{ $slot }}
</a>
