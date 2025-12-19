@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-cyan-400 text-start text-base font-medium text-slate-200 bg-[#23304A] focus:outline-none focus:text-slate-100 focus:bg-[#23304A] focus:border-cyan-500 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-400 hover:text-slate-200 hover:bg-[#162032] hover:border-cyan-400/50 focus:outline-none focus:text-slate-200 focus:bg-[#162032] focus:border-cyan-400/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
