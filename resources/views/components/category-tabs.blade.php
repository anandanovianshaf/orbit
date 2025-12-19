<ul class="flex flex-nowrap items-center gap-2 text-sm font-medium text-slate-400 whitespace-nowrap overflow-x-auto no-scrollbar sm:justify-center sm:overflow-x-visible">
    <li class="shrink-0">
        <a href="{{ route('dashboard') }}" class="{{
         request()->routeIs('post.byCategory')
         ? 'inline-block px-4 py-2 rounded-lg hover:text-slate-200 hover:bg-[#162032] hover:border-[#23304A] border border-transparent transition-colors'
         : 'inline-block px-4 py-2 text-white bg-violet-600 border border-violet-500 rounded-lg active' }}">
            All Repos
        </a>
    </li>

    @forelse($categories as $category)
        <li class="shrink-0">
            <a href="{{ route('post.byCategory', $category->id) }}" class="{{
             isset($currentCategory) && $currentCategory->id === $category->id
             ? 'inline-block px-4 py-2 text-white bg-violet-600 border border-violet-500 rounded-lg active'
             : 'inline-block px-4 py-2 rounded-lg hover:text-slate-200 hover:bg-[#162032] hover:border-[#23304A] border border-transparent transition-colors' }}">
                {{ $category->name }}
            </a>
        </li>
    @empty
        <li class="py-2 px-4 shrink-0">
            {{ $slot }}
        </li>
    @endforelse

</ul>
