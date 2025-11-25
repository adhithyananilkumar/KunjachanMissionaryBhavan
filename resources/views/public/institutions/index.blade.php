@extends('layouts.public')
@section('title','Institutions')
@section('content')
<section class="hero">
    <div class="container">
        <div class="surface mb-3">
            <div class="section-heading">Institutions</div>
            <h1 class="h3 mb-1">Our Institutions</h1>
            <p class="muted mb-0">Explore our homes and centers. Each institution has its own programs, gallery, and ways to support.</p>
        </div>
        <div class="row g-3">
            @foreach($institutions as $inst)
                <div class="col-md-6 col-lg-4">
                    <div class="surface h-100 p-0 overflow-hidden">
                        <a href="{{ route('institutions.show', $inst['slug']) }}" class="d-block">
                            <img src="{{ $inst['image'] }}" alt="{{ $inst['name'] }}" class="w-100" style="height:200px;object-fit:cover;">
                        </a>
                        <div class="p-3">
                            <h3 class="h5 mb-1"><a href="{{ route('institutions.show', $inst['slug']) }}">{{ $inst['name'] }}</a></h3>
                            <div class="muted small mb-2">{{ $inst['location'] }}</div>
                            <a href="{{ route('institutions.show', $inst['slug']) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">View details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection