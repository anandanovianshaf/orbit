@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-cyan-400 text-sm font-medium leading-5 text-slate-200 focus:outline-none focus:border-cyan-500 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-400 hover:text-slate-200 hover:border-cyan-400/50 focus:outline-none focus:text-slate-200 focus:border-cyan-400/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
