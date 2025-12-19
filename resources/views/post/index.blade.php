<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @unless(($hideCategories ?? false) === true)
                <div class="bg-[#162032]/50 border border-[#23304A] overflow-hidden sm:rounded-lg">
                    <div class="p-4">
                        <x-category-tabs :currentCategory="$currentCategory ?? null">
                            No Repos
                        </x-category-tabs>
                    </div>
                </div>
            @endunless
            <div class="mt-8">
                @forelse ($posts as $p)
                    <x-post-item :post="$p"></x-post-item>
                @empty
                    <div class="text-center text-slate-500 py-16 font-mono">No Logs Found</div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>