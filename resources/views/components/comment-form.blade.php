@props(['post'])

@auth
<div class="mt-8 pt-8 border-t border-[#23304A]">
    <h3 class="text-lg font-semibold mb-4 text-slate-200">Add a Thread</h3>
    
    <form action="{{ route('comment.store', $post) }}" method="POST">
        @csrf
        
        <div>
            <x-input-textarea 
                id="content" 
                name="content" 
                class="block mt-1 w-full" 
                rows="4"
                placeholder="Write your thread here..."
                required
            >{{ old('content') }}</x-input-textarea>
            <x-input-error :messages="$errors->get('content')" class="mt-2" />
        </div>
        
        <div class="mt-4">
            <x-primary-button>
                Post Thread
            </x-primary-button>
        </div>
    </form>
</div>
@else
<div class="mt-8 pt-8 border-t border-[#23304A]">
    <p class="text-slate-400">
        <a href="{{ route('login') }}" class="text-cyan-400 hover:text-cyan-300 hover:underline">Sign in</a> to leave a thread.
    </p>
</div>
@endauth

