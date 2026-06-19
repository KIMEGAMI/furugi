@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-t-xl border-b-2 border-emerald-300 bg-white/10 px-3 pt-1 text-sm font-black leading-5 text-emerald-100 transition duration-150 ease-in-out focus:border-emerald-200 focus:text-white focus:outline-none'
            : 'inline-flex items-center rounded-t-xl border-b-2 border-transparent px-3 pt-1 text-sm font-bold leading-5 text-slate-100 transition duration-150 ease-in-out hover:border-emerald-300 hover:bg-white/10 hover:text-white focus:border-emerald-300 focus:bg-white/10 focus:text-white focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
