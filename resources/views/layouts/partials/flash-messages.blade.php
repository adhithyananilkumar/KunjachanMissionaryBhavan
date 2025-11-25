@php
    $flashSuccess = session('success') ?? session('status');
    $flashError = session('error');
    $flashWarning = session('warning');
    $flashInfo = session('info');
@endphp

<div class="container-xxl px-0 mb-2">
    @if($flashSuccess)
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <span class="bi bi-check-circle-fill me-2"></span>
            <div>{{ $flashSuccess }}</div>
        </div>
    @endif
    @if($flashError)
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <span class="bi bi-exclamation-octagon-fill me-2"></span>
            <div>{{ $flashError }}</div>
        </div>
    @endif
    @if($flashWarning)
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <span class="bi bi-exclamation-triangle-fill me-2"></span>
            <div>{{ $flashWarning }}</div>
        </div>
    @endif
    @if($flashInfo)
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <span class="bi bi-info-circle-fill me-2"></span>
            <div>{{ $flashInfo }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<script>
 document.addEventListener('DOMContentLoaded', function(){
        if(typeof toastr !== 'undefined'){
                toastr.options = {
                        closeButton: true,
                        progressBar: true,
                        newestOnTop: true,
                        timeOut: 6000,
                        extendedTimeOut: 3000,
                        positionClass: 'toast-top-right',
                };
                @if($flashSuccess)
                        toastr.success(@json($flashSuccess));
                @endif
                @if($flashError)
                        toastr.error(@json($flashError));
                @endif
                @if($flashWarning)
                        toastr.warning(@json($flashWarning));
                @endif
                @if($flashInfo)
                        toastr.info(@json($flashInfo));
                @endif
                @if($errors->any())
                        @foreach($errors->all() as $e)
                                toastr.error(@json($e));
                        @endforeach
                @endif
        }
 });
</script>
