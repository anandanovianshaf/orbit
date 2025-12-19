<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-[#162032]/50 border border-[#23304A] sm:rounded-lg">
                <div class="flex flex-col gap-6 sm:flex-row sm:gap-0">
                    <div class="order-2 sm:order-1 flex-1 sm:pr-8">
                        <h1 class="text-5xl text-slate-200 font-bold">{{ $user->name }}</h1>

                        <div class="mt-8">
                            @forelse ($posts as $p)
                                <x-post-item :post="$p"></x-post-item>
                            @empty
                                <div class="text-center text-slate-500 py-16 font-mono">No Logs Found</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="order-1 sm:order-2">
                        <x-follow-ctr :user="$user">
                        <x-user-avatar :user="$user" size="w-24 h-24" />
                        <h3 class="text-slate-200 font-bold">{{ $user->name }}</h3>
                        <p class="text-slate-400 font-mono">
                        <span x-text="followersCount"></span> links</p>
                        <p class="text-slate-300">
                            {{ $user->bio }}
                        </p>
                        @if (auth()->user() && auth()->user()->id !== $user->id)
                            <div class="mt-4">
                                <button @click="follow()" class="rounded-full px-4 py-2 text-white font-mono text-sm transition-colors"
                                x-text="following ? 'Unlink' : 'Link'"
                                :class="following ? 'bg-red-600 hover:bg-red-500' : 'bg-cyan-600 hover:bg-cyan-500'"
                                >
                                    
                                </button>
                            </div>
                        @endif
                        </x-follow-ctr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
