<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#162032]/50 border border-[#23304A] overflow-hidden sm:rounded-lg p-8">
                <h1 class="text-2xl mb-4 text-slate-200 font-bold">{{ $post->title }}</h1>

                <!-- User Avatar -->
                <div class="flex gap-4">
                    <x-user-avatar :user="$post->user" />

                    <div>
                        <x-follow-ctr :user="$post->user" class="flex gap-2">
                            <a href="{{ route('profile.show', $post->user) }}" class="text-slate-200 hover:text-cyan-400 hover:underline transition-colors">
                                {{ $post->user->name }}
                            </a>
                            
                            @auth
                                &middot;
                                <button x-text="following ? 'Unlink' : 'Link'" 
                                :class="following ? 'text-red-400 hover:text-red-300' : 'text-cyan-400 hover:text-cyan-300'"
                                @click="follow()"
                                class="transition-colors font-mono text-sm">
                                </button>
                            @endauth
                        </x-follow-ctr>

                        <div class="flex gap-2 text-sm text-slate-500 font-mono">
                            {{ $post->readTime() }} min read
                            &middot;
                            {{ $post->created_at->format('M d, Y') }}
                        </div>
                    </div>

                </div>
                <!-- User Avatar -->

                @if ($post->user_id === Auth::id())
                    <div class="py-4 mt-8 border-t border-b border-[#23304A]">
                        <x-primary-button href="{{ route('post.edit', $post->slug) }}">
                            Edit Log
                        </x-primary-button>
                        <form class="inline-block ml-2" action="{{ route('post.destroy', $post) }}" method="post">
                            @csrf
                            @method('delete')
                            <x-danger-button>
                                Delete Log
                            </x-danger-button>
                        </form>
                    </div>
                @endif

                <!-- Content Section -->
                <div class="mt-8">
                    @if($post->imageUrl())
                        <div class="mb-6 flex justify-center">
                            <img
                                src="{{ $post->imageUrl() }}"
                                alt="{{ $post->title }}"
                                class="w-full max-w-3xl max-h-[420px] object-cover rounded-lg border border-[#23304A]"
                                loading="lazy"
                            >
                        </div>
                    @endif

                    @php
                        // Render as plain text with tighter spacing.
                        // - Keep user newlines as <br> (no paragraph splitting).
                        // - Neutralize tab characters so they don't create big indents.
                        $content = str_replace("\\" . "t", "", (string) $post->content);
                    @endphp

                    <div class="mt-6 text-slate-200 leading-7 text-[1.05rem] break-words">
                        {!! nl2br(e($content)) !!}
                    </div>
                </div>

                <div class="mt-8">
                    <span class="px-4 py-2 bg-violet-600/20 border border-violet-500/50 text-violet-400 rounded-2xl font-mono text-sm">
                        {{ $post->category->name }}
                    </span>
                </div>

                <!-- Ack + Views (bottom) -->
                <div class="mt-8 p-4 border-t border-b border-[#23304A] flex items-center justify-between gap-4">
                    @auth
                        <x-clap-button :post="$post" inline />
                    @endauth

                    <div class="flex items-center gap-2 text-slate-400 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span class="font-mono">{{ number_format($post->views) }}</span>
                        <span class="text-sm">Views</span>
                    </div>
                </div>
                <!-- Ack + Views (bottom) -->
            </div>

            <!-- Threads Section -->
            <div class="mt-8 bg-[#162032]/50 border border-[#23304A] overflow-hidden sm:rounded-lg p-8">
                @if(session('success'))
                    <div 
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        class="mb-4 p-4 bg-cyan-500/20 border border-cyan-400/50 text-cyan-400 rounded-lg font-mono text-sm"
                    >
                        {{ session('success') }}
                    </div>
                @endif

                <h2 class="text-xl font-semibold mb-6 text-slate-200">
                    Threads ({{ $post->comments->count() }})
                </h2>

                <!-- Threads List -->
                <div class="space-y-4">
                    @forelse($post->comments as $comment)
                        <x-comment-item :comment="$comment" />
                    @empty
                        <div class="text-center text-slate-500 py-8 font-mono">
                            No threads yet. Be the first to thread!
                        </div>
                    @endforelse
                </div>

                <!-- Thread Form -->
                <x-comment-form :post="$post" />
            </div>
            <!-- Threads Section -->
        </div>
    </div>
</x-app-layout>