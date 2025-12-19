<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#162032]/50 border border-[#23304A] overflow-hidden sm:rounded-lg p-8 mb-8">
                <h1 class="text-2xl font-bold text-slate-200 mb-2">Search Results</h1>
                <p class="text-slate-400 font-mono">
                    @if($query)
                        Results for: <span class="text-cyan-400">"{{ $query }}"</span>
                    @else
                        Enter a search query
                    @endif
                </p>
            </div>

            <div>
                @forelse ($posts as $p)
                    <x-post-item :post="$p"></x-post-item>
                @empty
                    <div class="bg-[#162032]/50 border border-[#23304A] rounded-lg p-8 text-center">
                        <div class="text-slate-500 py-16 font-mono">
                            @if($query)
                                No logs found for "{{ $query }}"
                            @else
                                No search query provided
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

