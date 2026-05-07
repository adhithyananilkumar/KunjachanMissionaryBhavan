@if ($paginator->hasPages())
<nav class="aw-pager my-3" role="navigation" aria-label="Pagination Navigation">
  <ul class="pagination justify-content-center mb-0 flex-wrap gap-1">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <li class="page-item disabled" aria-disabled="true">
        <span class="page-link" aria-hidden="true"><span class="bi bi-chevron-left"></span></span>
      </li>
    @else
      <li class="page-item">
        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
          <span class="bi bi-chevron-left"></span>
        </a>
      </li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
          @else
            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <li class="page-item">
        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
          <span class="bi bi-chevron-right"></span>
        </a>
      </li>
    @else
      <li class="page-item disabled" aria-disabled="true">
        <span class="page-link" aria-hidden="true"><span class="bi bi-chevron-right"></span></span>
      </li>
    @endif
  </ul>
</nav>
@endif

@push('styles')
<style>
/* Compact, mobile-friendly pagination */
.aw-pager .page-link{ min-width:2.25rem; text-align:center; padding:.375rem .5rem; line-height:1.2; }
.aw-pager .page-item .page-link{ border-radius:.5rem; }
@media (max-width: 576px){ .aw-pager .page-link{ min-width:2rem; padding:.35rem .45rem; font-size:.875rem; } }
</style>
@endpush