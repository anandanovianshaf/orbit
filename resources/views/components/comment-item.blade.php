@props(['comment'])

<div class="flex gap-4 py-4 border-b border-[#23304A] last:border-b-0">
    <x-user-avatar :user="$comment->user" :size="'w-10 h-10'" />
    
    <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('profile.show', $comment->user->username) }}" class="font-semibold text-slate-200 hover:text-cyan-400 hover:underline transition-colors">
                {{ $comment->user->name }}
            </a>
            <span class="text-sm text-slate-500 font-mono">
                {{ $comment->created_at->diffForHumans() }}
            </span>
        </div>
        
        <div class="text-slate-300 mb-2 leading-7">
            @php
                $commentContent = str_replace("\\" . "t", "", (string) $comment->content);
            @endphp

            <div class="break-words leading-7">
                {!! nl2br(e($commentContent)) !!}
            </div>
        </div>
        
        @auth
            @if($comment->user_id === auth()->id() || $comment->post->user_id === auth()->id())
                <form action="{{ route('comment.destroy', $comment) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition-colors font-mono">
                        [DELETE]
                    </button>
                </form>
            @endif
        @endauth
    </div>
</div>

