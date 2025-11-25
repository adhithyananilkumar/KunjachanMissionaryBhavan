@extends('layouts.guest')

@section('title','Log in')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
      <div class="aw-card p-4 p-md-5">
        <div class="text-center mb-3">
          <img src="{{ asset('assets/aathmiya.png') }}" alt="Aathmiya" style="height:38px;width:auto">
        </div>
        <h1 class="h3 mb-2 text-center">Welcome back</h1>
        <p class="text-secondary mb-4 text-center">Sign in to continue to Aathmiya.</p>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="button" class="btn btn-link p-0 aw-link" data-bs-toggle="modal" data-bs-target="#forgotModal">Forgot password?</button>
          </div>

          <button type="submit" class="btn aw-btn-primary text-white w-100 py-2 rounded-pill">
            <i class="bi bi-box-arrow-in-right me-1"></i> Log in
          </button>
        </form>

        @if (Route::has('register'))
        <div class="text-center mt-3">
          <span class="text-muted">New here?</span>
          <a class="btn btn-outline-dark rounded-pill ms-2 px-3" href="{{ route('register') }}">Create account</a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Forgot password modal -->
<div class="modal fade" id="forgotModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-question-circle me-1"></i> Forgot password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Please contact your administrator to reset your account password.</p>
        <ul class="mb-0 text-secondary">
          <li>Email: support@aathmiya.local</li>
          <li>Or reach out to your institution’s system admin.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('footer')
<div>&copy; {{ date('Y') }} AJCE24BCA</div>
@endsection
