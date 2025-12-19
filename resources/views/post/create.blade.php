<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl mb-4 text-slate-200 font-bold">Create New Log</h1>
            
            <div class="bg-[#162032]/50 border border-[#23304A] overflow-hidden sm:rounded-lg p-8 backdrop-blur-sm">
                <form action="{{  route('post.store') }}"
                enctype="multipart/form-data" method="post">

                    @csrf

                    <div>
                        <x-input-label for="image" :value="__('Image')" class="text-slate-300" />
                        <x-text-input id="image" 
                            class="block mt-1 w-full bg-[#050912] border-[#23304A] text-slate-200 focus:border-cyan-400 focus:ring-cyan-400 file:bg-slate-700 file:text-white file:border-0 file:mr-4 file:py-2 file:px-4 file:rounded-full file:text-sm" 
                            type="file" name="image"
                            :value="old('image')" autofocus />
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="title" :value="__('Title')" class="text-slate-300" />
                        <x-text-input id="title" 
                            class="block mt-1 w-full bg-[#050912] border-[#23304A] text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-cyan-400 font-mono" 
                            type="text" name="title"
                            :value="old('title')" autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="category_id" :value="__('Repo')" class="text-slate-300" />
                        <select id="category_id" name="category_id" class="bg-[#050912] border border-[#23304A] text-white font-mono focus:border-cyan-400 focus:ring-0 rounded-md shadow-sm block mt-1 w-full">
                            <option value="" class="bg-[#050912]">Select a Repo</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" class="bg-[#050912]"
                                    @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="content" :value="__('Content')" class="text-slate-300" />
                        <textarea id="content" 
                            class="block mt-1 w-full bg-[#050912] border-[#23304A] text-white font-mono focus:border-cyan-400 focus:ring-cyan-400 rounded-md shadow-sm h-40" 
                            name="content">{{ old('content') }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="published_at" :value="__('Published At')" class="text-slate-300" />
                        <x-text-input id="published_at" 
                            class="block mt-1 w-full bg-[#050912] border-[#23304A] text-white focus:border-cyan-400 focus:ring-cyan-400 [&::-webkit-calendar-picker-indicator]:invert" 
                            type="datetime-local" name="published_at"
                            :value="old('published_at')" autofocus />
                        <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                    </div>

                    <x-primary-button class="mt-6 bg-violet-600 hover:bg-violet-500 border-none">
                        Submit Log
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>