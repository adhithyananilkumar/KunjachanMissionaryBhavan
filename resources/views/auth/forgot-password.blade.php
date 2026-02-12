<x-guest-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-6 col-xl-5">
                <div class="kb-card p-4 p-md-5">
                    <div class="section-heading mb-2">Password Recovery</div>
                    <h1 class="h3 mb-2" style="color: var(--kb-primary);">Forgot your password?</h1>
                    <p class="muted mb-4">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success py-2">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        <button type="submit" class="btn kb-btn-primary text-white w-100 py-2 rounded-pill">
                            <i class="bi bi-send me-1"></i> {{ __('Email Password Reset Link') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
