<x-guest-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-6 col-xl-5">
                <div class="kb-card p-4 p-md-5">
                    <div class="section-heading mb-2">Security</div>
                    <h1 class="h3 mb-2" style="color: var(--kb-primary);">{{ __('Confirm password') }}</h1>
                    <p class="muted mb-4">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
                            </div>
                        </div>

                        <button type="submit" class="btn kb-btn-primary text-white w-100 py-2 rounded-pill">
                            <i class="bi bi-check2-circle me-1"></i> {{ __('Confirm') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
