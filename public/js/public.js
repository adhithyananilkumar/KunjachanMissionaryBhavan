// Simple page loader hide on load
(function(){
  var loader = document.getElementById('page-loader');
  function hide(){ if(loader){ loader.classList.add('hidden'); } }
  window.addEventListener('load', function(){ setTimeout(hide, 250); });
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
