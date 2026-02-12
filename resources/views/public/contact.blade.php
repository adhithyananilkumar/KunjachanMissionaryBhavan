@extends('layouts.public')
@section('title','Contact')
@section('content')
<section class="hero">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="surface h-100">
                    <div class="section-heading">Contact</div>
                    <h1 class="h3">We'd love to hear from you</h1>
                    <p class="muted">Reach out with questions, visit requests, or ways to support the mission.</p>
                    <ul class="list-plain muted">
                        <li class="mb-1"><strong>Address:</strong> Kunjachan Missionary Bhavan, Idiyanal P.O, Ramapuram, Kottayam, Kerala</li>
                        <li class="mb-1"><strong>Email:</strong> <a href="mailto:kunjachanmissionary@gmail.com">kunjachanmissionary@gmail.com</a></li>
                        <li class="mb-1"><strong>Phone:</strong> <a href="tel:+918281960435">+91 82819 60435</a></li>
                        <li class="mb-1"><strong>Map:</strong> <a target="_blank" rel="noopener" href="https://maps.app.goo.gl/NP69bHdpmoBK2MY37">Open in Google Maps</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="surface h-100">
                    <div class="section-heading">Send a message</div>
                    @if(session('success'))
                        <div class="alert alert-success mb-3">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('contact.store') }}" method="post">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Your name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Message</label>
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" placeholder="How can we help?" required>{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button class="btn btn-kb rounded-pill px-3" type="submit">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection