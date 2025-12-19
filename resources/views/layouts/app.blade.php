<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0B1120">

    <title>{{ config('app.name', 'Orbit') }}</title>

        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/pwa/icon-192.png">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data class="font-sans antialiased bg-[#0B1120] text-slate-200">
        <div class="min-h-screen opacity-0" data-page-root>
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="backdrop-blur-md bg-[#0B1120]/80 border-b border-[#23304A]">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <div id="page-loader" class="fixed inset-0 pointer-events-none opacity-0 transition-opacity duration-150 ease-orbit" aria-hidden="true">
            <div class="absolute inset-0 bg-[#0B1120]/35 backdrop-blur-[2px]"></div>
            <div class="absolute left-1/2 top-20 -translate-x-1/2 w-[min(720px,92vw)] space-y-3">
                <div class="skeleton h-5 w-2/3"></div>
                <div class="skeleton h-4 w-1/3"></div>
                <div class="skeleton h-24 w-full"></div>
            </div>
        </div>

        <script>
            // Simple, lightweight page transitions (no SPA):
            // - fade in on load
            // - fade out on internal navigation
            (() => {
                const root = document.querySelector('[data-page-root]');
                if (!root) return;

                const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const durationIn = prefersReduced ? 1 : 60;
                const durationOut = prefersReduced ? 1 : 40;

                // Fade in
                requestAnimationFrame(() => {
                    root.style.transition = `opacity ${durationIn}ms cubic-bezier(0.2,0.8,0.2,1)`;
                    root.style.opacity = '1';
                });

                // Fade out on internal links
                document.addEventListener('click', (e) => {
                    const a = e.target && e.target.closest ? e.target.closest('a') : null;
                    if (!a) return;

                    const href = a.getAttribute('href') || '';
                    if (!href || href.startsWith('#')) return;
                    if (a.hasAttribute('download')) return;
                    const target = a.getAttribute('target');
                    if (target && target !== '_self') return;
                    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

                    // Same-origin only
                    let url;
                    try {
                        url = new URL(a.href, window.location.href);
                    } catch {
                        return;
                    }
                    if (url.origin !== window.location.origin) return;
                    if (url.pathname === window.location.pathname && url.search === window.location.search) return;

                    e.preventDefault();
                    const loader = document.getElementById('page-loader');
                    if (loader) loader.style.opacity = '1';
                    root.style.transition = `opacity ${durationOut}ms cubic-bezier(0.2,0.8,0.2,1)`;
                    root.style.opacity = '0';
                    window.setTimeout(() => {
                        window.location.href = url.href;
                    }, durationOut);
                }, { capture: true });
            })();
        </script>
    </body>
</html>
