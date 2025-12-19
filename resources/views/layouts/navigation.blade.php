<nav x-data="{ open: false }" data-parallax-nav class="sticky top-0 z-50 backdrop-blur-md bg-[#0B1120]/80 border-b border-[#23304A] will-change-transform">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 py-3 sm:py-0 sm:flex-row sm:items-center sm:justify-between sm:h-16">
            <div class="flex items-center justify-between sm:justify-start">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <span class="text-2xl font-mono font-bold text-violet-500 tracking-wider">ORBIT</span>
                    </a>
                </div>
                
                <!-- Hamburger (mobile only) -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-200 hover:bg-[#162032] focus:outline-none focus:bg-[#162032] focus:text-slate-200 transition duration-200 ease-orbit">
                        <svg class="h-6 w-6 transition-transform duration-200 ease-orbit" :class="open ? 'rotate-90' : 'rotate-0'" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-1 justify-center sm:mx-6 sm:max-w-md">
                <!-- Search Input -->
                <div x-data="{ 
                    query: '',
                    results: [],
                    loading: false,
                    showResults: false,
                    search() {
                        if (this.query.length < 2) {
                            this.results = [];
                            this.showResults = false;
                            return;
                        }
                        this.loading = true;
                        axios.get('/search', { 
                            params: { q: this.query },
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => {
                                this.results = response.data.posts.data || [];
                                this.showResults = true;
                                this.loading = false;
                            })
                            .catch(error => {
                                console.error(error);
                                this.results = [];
                                this.loading = false;
                            });
                    },
                    hideResults() {
                        setTimeout(() => this.showResults = false, 200);
                    }
                }" class="relative w-full" @click.away="showResults = false">
                    <input 
                        type="text" 
                        x-model="query"
                        @input="search()"
                        @focus="query.length >= 2 && results.length > 0 ? showResults = true : null"
                        placeholder="Search logs..."
                        class="w-full bg-[#050912] border border-[#23304A] text-white font-mono focus:border-cyan-400 focus:ring-0 rounded-md shadow-sm px-4 py-2 text-sm"
                    >
                    <div x-show="loading" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div 
                        x-show="showResults && (results.length > 0 || query.length >= 2)"
                        x-transition:enter="transition ease-orbit duration-180"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-orbit duration-120"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                        class="absolute z-50 mt-2 w-full bg-[#162032] border border-[#23304A] rounded-lg shadow-lg max-h-96 overflow-y-auto"
                        style="display: none;"
                    >
                        <template x-if="loading">
                            <div class="p-4 space-y-3">
                                <div class="skeleton h-4 w-3/4"></div>
                                <div class="skeleton h-3 w-1/2"></div>
                                <div class="skeleton h-4 w-5/6"></div>
                                <div class="skeleton h-3 w-2/5"></div>
                            </div>
                        </template>

                        <template x-if="results.length === 0 && query.length >= 2 && !loading">
                            <div class="p-4 text-center text-slate-400 font-mono text-sm">
                                No logs found
                            </div>
                        </template>
                        <template x-if="results.length > 0">
                            <div>
                                <template x-for="post in results" :key="post.id">
                                    <a 
                                        :href="`/@${post.user.username}/${post.slug}`"
                                        class="block px-4 py-3 hover:bg-[#23304A] border-b border-[#23304A] last:border-b-0 transition-colors"
                                    >
                                        <div class="font-semibold text-slate-200 text-sm mb-1" x-text="post.title"></div>
                                        <div class="text-xs text-slate-400 font-mono" x-text="post.user.name"></div>
                                    </a>
                                </template>
                                <div class="p-2 border-t border-[#23304A]">
                                    <a 
                                        :href="`/search?q=${encodeURIComponent(query)}`"
                                        class="block text-center text-sm text-cyan-400 hover:text-cyan-300 font-mono"
                                    >
                                        View all results →
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 justify-end">
                <a href="{{ route('post.create') }}" class="hidden sm:flex items-center">
                    <x-primary-button>
                        New Log
                    </x-primary-button>
                </a>

                @auth
                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-2">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-[#23304A] text-sm leading-4 font-medium rounded-md text-slate-200 bg-[#162032]/50 hover:bg-[#162032] hover:border-cyan-400/50 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('myPosts')">
                                    {{ __('My Logs') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth

                @guest
                    <a href="{{  route('register') }}"  class="hidden sm:flex items-center px-3 py-2 border border-[#23304A] text-sm leading-4 font-medium rounded-md text-slate-200 bg-[#162032]/50 hover:bg-[#162032] hover:border-cyan-400/50 focus:outline-none transition ease-in-out duration-150">
                        Create an Account
                    </a>
                    <a href="{{  route('login') }}" class="hidden sm:flex items-center px-3 py-2 border border-[#23304A] text-sm leading-4 font-medium rounded-md text-slate-200 bg-[#162032]/50 hover:bg-[#162032] hover:border-cyan-400/50 focus:outline-none transition ease-in-out duration-150">
                       Login
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (mobile) -->
    <div
        x-show="open"
        x-transition:enter="transition ease-orbit duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-orbit duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="sm:hidden"
        style="display: none;"
    >

        <div class="px-4 pb-3">
            <a href="{{ route('post.create') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-violet-600 border border-violet-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-[#0B1120] transition">
                New Log
            </a>
        </div>

        @auth
            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-[#23304A]">
                <div class="px-4">
                    <div class="font-medium text-base text-slate-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-400">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('myPosts')">
                        {{ __('My Logs') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth

        @guest
            <div class="pt-4 pb-1 border-t border-[#23304A]">
                <div class="px-4 space-y-1">
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Create an Account') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endguest
    </div>
</nav>
