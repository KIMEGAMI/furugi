@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-emerald-300 bg-white/10 py-2 pe-4 ps-3 text-start text-base font-black text-emerald-100 transition duration-150 ease-in-out focus:border-emerald-200 focus:bg-white/15 focus:text-white focus:outline-none'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-bold text-slate-100 transition duration-150 ease-in-out hover:border-emerald-300 hover:bg-white/10 hover:text-white focus:border-emerald-300 focus:bg-white/10 focus:text-white focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
