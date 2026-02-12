// Page loader: show only when actually loading
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

  // Hide immediately by default; we'll only show if needed.
  hide();

  // If the page is still loading after a short delay, show loader.
  var slowLoadTimer = window.setTimeout(function () {
    if (document.readyState !== 'complete') show();
  }, 250);

  window.addEventListener('load', function () {
    window.clearTimeout(slowLoadTimer);
    hide();
  });

  // Back/forward cache restores can leave stale UI; ensure hidden.
  window.addEventListener('pageshow', function () {
    hide();
  });

  // Show loader for real same-origin navigations.
  document.addEventListener('click', function (e) {
    if (e.defaultPrevented) return;
    if (e.button !== 0) return; // left-click only
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

    var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
    if (!a) return;

    if (a.hasAttribute('download')) return;
    if (a.getAttribute('target') && a.getAttribute('target') !== '_self') return;
    if (a.hasAttribute('data-bs-toggle')) return; // bootstrap components (modal, dropdown)

    var href = (a.getAttribute('href') || '').trim();
    if (!href || href === '#') return;
    if (href.indexOf('javascript:') === 0) return;
    if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;

    try {
      var url = new URL(a.href, window.location.href);
      if (url.origin !== window.location.origin) return;

      // Same page hash changes shouldn't show loader.
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

  // Show loader on same-window form submits.
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    if (form.getAttribute('target') && form.getAttribute('target') !== '_self') return;
    show();
  });
})();

// Blog feed client-side search (demo)
(function(){
  var input = document.getElementById('feedSearch');
  var feed = document.getElementById('feed');
  if(!input || !feed) return;
  var items = Array.from(feed.querySelectorAll('.feed-item'));
  input.addEventListener('input', function(){
    var q = this.value.trim().toLowerCase();
    items.forEach(function(it){
      var text = (it.getAttribute('data-text') || it.textContent || '').toLowerCase();
      it.style.display = q === '' || text.indexOf(q) !== -1 ? '' : 'none';
    });
  });
})();
