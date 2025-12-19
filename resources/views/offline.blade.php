<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#162032]/50 border border-[#23304A] overflow-hidden sm:rounded-lg p-8">
                <h1 class="text-2xl font-bold text-slate-200">You're offline</h1>
                <p class="mt-3 text-slate-400 leading-7">
                    It looks like you don't have an internet connection right now.
                    You can still browse pages you've opened recently.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-cyan-600 hover:bg-cyan-500 text-white font-mono text-sm transition">
                        Go to Dashboard
                    </a>
                    <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 rounded-md bg-[#23304A] hover:bg-[#2b3b5a] text-slate-200 font-mono text-sm transition">
                        Retry
                    </button>
                </div>

                <div class="mt-8 text-xs text-slate-500 font-mono">
                    Tip: In Chrome DevTools → Application → Service Workers, you can toggle “Offline” to test.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
