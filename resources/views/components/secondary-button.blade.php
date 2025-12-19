<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[#162032] border border-[#23304A] rounded-md font-mono text-xs text-slate-200 uppercase tracking-widest shadow-sm hover:bg-[#23304A] hover:border-cyan-400/50 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-[#0B1120] disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
