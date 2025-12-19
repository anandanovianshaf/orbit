import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PWA: register service worker
(() => {
	if (!('serviceWorker' in navigator)) return;

	const isLocalhost = ['localhost', '127.0.0.1', '[::1]'].includes(window.location.hostname);
	const isSecure = window.isSecureContext || isLocalhost;

	const register = () => {
		if (!isSecure) {
			console.warn('Service Worker requires HTTPS (or http://localhost). Current origin is not a secure context:', window.location.origin);
			// Optional little flag for debugging (useful while developing)
			document.documentElement.dataset.swBlocked = '1';
			return;
		}

		navigator.serviceWorker
			.register('/sw.js', { scope: '/' })
			.then(() => {
				document.documentElement.dataset.swRegistered = '1';
			})
			.catch((err) => console.warn('SW registration failed', err));
	};

	// Register as early as possible
	if (document.readyState === 'complete') {
		register();
	} else {
		window.addEventListener('load', register);
	}
})();

// --- Micro-interactions (lightweight, non-SPA) ---
(() => {
	const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (prefersReduced) return;

	// Reveal-on-scroll cards
	const revealEls = () => Array.from(document.querySelectorAll('[data-reveal]'));
	const supportsIO = 'IntersectionObserver' in window;

	if (supportsIO) {
		const io = new IntersectionObserver(
			(entries) => {
				for (const entry of entries) {
					if (!entry.isIntersecting) continue;
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			},
			{ root: null, threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
		);

		revealEls().forEach((el) => io.observe(el));

		// If your pages are partially updated by Turbo/etc (not currently), this keeps it safe.
		document.addEventListener('DOMContentLoaded', () => revealEls().forEach((el) => io.observe(el)));
	} else {
		// Fallback: just show
		revealEls().forEach((el) => el.classList.add('is-visible'));
	}

	// Subtle parallax on nav background
	const nav = document.querySelector('[data-parallax-nav]');
	if (!nav) return;

	let ticking = false;
	const onScroll = () => {
		if (ticking) return;
		ticking = true;
		window.requestAnimationFrame(() => {
			const y = Math.max(0, Math.min(window.scrollY, 200));
			nav.style.transform = `translate3d(0, ${y * 0.05}px, 0)`;
			ticking = false;
		});
	};

	window.addEventListener('scroll', onScroll, { passive: true });
})();
