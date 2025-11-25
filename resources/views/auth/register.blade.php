@extends('layouts.guest')

@section('title','Create account')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-6 col-xl-5">
            <div class="aw-card p-4 p-md-5">
                <div class="text-center mb-3">
                    <img src="{{ asset('assets/aathmiya.png') }}" alt="Aathmiya" style="height:38px;width:auto">
                </div>
                <h1 class="h3 mb-2 text-center">Create your account</h1>
                <p class="text-secondary mb-4 text-center">Set up your access to Aathmiya.</p>

                @if ($errors->any())
                        <div class="alert alert-danger py-2">
                                <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                        @endforeach
                                </ul>
                        </div>
                @endif

                <form method="POST" action="{{ route('register') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus autocomplete="name">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autocomplete="username">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
                        </div>
                    </div>

                        <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn aw-btn-primary text-white w-100 py-2 rounded-pill">
                        <i class="bi bi-person-plus me-1"></i> Create account
                    </button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted">Already have an account?</span>
                    <a class="btn btn-outline-dark rounded-pill ms-2 px-3" href="{{ route('login') }}">Log in</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<div>&copy; {{ date('Y') }} AJCE24BCA</div>
@endsection

