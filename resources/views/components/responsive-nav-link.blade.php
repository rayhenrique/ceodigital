@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full ps-3 pe-4 py-2.5 border-l-4 border-teal-600 text-start text-sm font-bold text-teal-800 bg-teal-50/80 focus:outline-none focus:text-teal-900 focus:bg-teal-100 transition duration-150 ease-in-out rounded-r-lg'
            : 'flex items-center w-full ps-3 pe-4 py-2.5 border-l-4 border-transparent text-start text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:text-slate-800 focus:bg-slate-50 transition duration-150 ease-in-out rounded-r-lg';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
