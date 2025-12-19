@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-[#050912] border border-[#23304A] text-white font-mono focus:border-cyan-400 focus:ring-0 rounded-md shadow-sm']) }}>
