import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Public site behavior (previously loaded via public/js/public.js)
// Guarded by element existence so it is safe on auth/admin pages.
(function () {
	var loader = document.getElementById('page-loader');
	if (!loader) return;

	function show() {
		loader.classList.add('active');
		loader.classList.remove('hidden');
	}

	function hide() {
		loader.classList.remove('active');
		loader.classList.add('hidden');
	}

	hide();

	var slowLoadTimer = window.setTimeout(function () {
		if (document.readyState !== 'complete') show();
	}, 250);

	window.addEventListener('load', function () {
		window.clearTimeout(slowLoadTimer);
		hide();
	});

	window.addEventListener('pageshow', function () {
		hide();
	});

	document.addEventListener('click', function (e) {
		if (e.defaultPrevented) return;
		if (e.button !== 0) return;
		if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

		var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
		if (!a) return;

		if (a.hasAttribute('download')) return;
		if (a.getAttribute('target') && a.getAttribute('target') !== '_self') return;
		if (a.hasAttribute('data-bs-toggle')) return;

		var href = (a.getAttribute('href') || '').trim();
		if (!href || href === '#') return;
		if (href.indexOf('javascript:') === 0) return;
		if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;

		try {
			var url = new URL(a.href, window.location.href);
			if (url.origin !== window.location.origin) return;

			if (
				url.pathname === window.location.pathname &&
				url.search === window.location.search &&
				url.hash &&
				url.hash.length > 1
			) {
				return;
			}
		} catch (_) {
			return;
		}

		show();
	});

	document.addEventListener('submit', function (e) {
		var form = e.target;
		if (!form || form.tagName !== 'FORM') return;
		if (form.getAttribute('target') && form.getAttribute('target') !== '_self') return;
		show();
	});
})();

(function () {
	var input = document.getElementById('feedSearch');
	var feed = document.getElementById('feed');
	if (!input || !feed) return;
	var items = Array.from(feed.querySelectorAll('.feed-item'));
	input.addEventListener('input', function () {
		var q = this.value.trim().toLowerCase();
		items.forEach(function (it) {
			var text = (it.getAttribute('data-text') || it.textContent || '').toLowerCase();
			it.style.display = q === '' || text.indexOf(q) !== -1 ? '' : 'none';
		});
	});
})();
