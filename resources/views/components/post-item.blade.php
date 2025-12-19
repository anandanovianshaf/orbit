<div data-reveal class="reveal-on-scroll flex flex-col sm:flex-row bg-[#162032]/50 border border-[#23304A] rounded-lg mb-8 sm:overflow-visible overflow-hidden hover-lift glow-hover">
    @if($post->imageUrl())
        <a href="{{ route('post.show', [
                'username' => $post->user->username,
                'post' => $post->slug
            ]) }}" class="block sm:order-2 sm:self-stretch sm:w-48 sm:shrink-0">
            <img
                class="w-full h-48 object-cover sm:w-48 sm:max-w-48 sm:h-full sm:max-h-64 sm:rounded-r-lg"
                src="{{ $post->imageUrl() }}"
                alt="{{ $post->title }}"
                loading="lazy"
            />
        </a>
    @endif

    <div class="p-5 flex-1 min-w-0 sm:order-1">
        <a href="{{ route('post.show', [
            'username' => $post->user->username,
            'post' => $post->slug
        ]) }}">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-slate-200 hover:text-cyan-400 transition-colors">
                {{ $post->title }}
            </h5>
        </a>
        <div class="mb-3 font-normal text-slate-400 break-words line-clamp-3">
            {{ Str::words(trim($post->content), 28) }}
        </div>
        <div class="text-sm text-slate-500 flex gap-4 flex-col sm:flex-row sm:items-center">
            <div class="">
                by
                <a href="{{ route('profile.show', $post->user->username) }}" class="text-cyan-400 hover:text-cyan-300 hover:underline">
                    {{ $post->user->username }}
                </a>
                at
                <span class="font-mono">{{ $post->created_at->format('M d, Y') }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            <span class="inline-flex gap-1 items-center text-cyan-400 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
                <span class="font-mono">{{  $post->claps_count }}</span> Ack
            </span>

            <span class="inline-flex gap-1 items-center text-slate-400 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="font-mono">{{ number_format($post->views ?? 0) }}</span>
                <span>Views</span>
            </span>

            <span class="inline-flex gap-1 items-center text-slate-400 whitespace-nowrap">
                <!-- Thread/Comment icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3a49.5 49.5 0 0 1-4.02-.163 2.115 2.115 0 0 1-1.825-1.583m10.845-8.334a2.126 2.126 0 0 0-1.98-2.193 48.426 48.426 0 0 0-11.52 0A2.126 2.126 0 0 0 3 6.39v4.286c0 1.136.847 2.1 1.98 2.193.34.027.68.052 1.02.072v3.091l3-3c1.327.065 2.646.11 3.96.136" />
                </svg>
                <span class="font-mono">{{ number_format($post->comments_count ?? 0) }}</span>
                <span>Threads</span>
            </span>
            </div>
        </div>
    </div>
</div>
