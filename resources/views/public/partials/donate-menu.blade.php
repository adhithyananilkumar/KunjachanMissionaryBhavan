<div class="surface">
    <h2 class="h5 mb-2">{{ $title }}</h2>
    <div class="row g-2">
        @foreach($pricing as [$amount,$label])
        <div class="col-6 col-md-3">
            <div class="surface text-center">
                <div class="h4 mb-0">{{ $amount }}</div>
                <div class="small muted">{{ $label }}</div>
                <button class="btn btn-kb btn-sm rounded-pill px-3 mt-2" disabled>Donate (demo)</button>
            </div>
        </div>
        @endforeach
    </div>
</div>
