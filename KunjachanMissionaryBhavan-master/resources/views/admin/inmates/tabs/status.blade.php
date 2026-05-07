@php
    $events = $inmate->statusEvents()->with('creator')->get();
    $status = $inmate->status ?: \App\Models\Inmate::STATUS_PRESENT;
    $hasDeathCert = $events->contains(function($ev){
        $atts = collect($ev->attachments ?? []);
        return $atts->contains(fn($a) => data_get($a,'type') === 'death_certificate');
    });
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <div class="fw-semibold">Lifecycle Status</div>
        <div class="small text-muted">Current: @include('partials.inmates._status_badge', ['status' => $status])</div>
    </div>
    <div class="small text-muted">
        @if($status !== \App\Models\Inmate::STATUS_PRESENT)
            This profile is read-only for most actions until re-joined.
        @endif
        @if($status === \App\Models\Inmate::STATUS_DECEASED)
            Status is permanently locked.
        @endif
    </div>
</div>

@if($status === \App\Models\Inmate::STATUS_DECEASED && !$hasDeathCert)
    <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="fw-semibold">Death certificate missing</div>
            <div class="small text-muted">This is the only upload allowed after marking as deceased.</div>
        </div>
        <form method="POST" action="{{ route('admin.inmates.status.death-certificate', $inmate) }}" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
            @csrf
            <input type="file" name="death_certificate" class="form-control form-control-sm" required accept=".pdf,.jpg,.jpeg,.png,.webp,.heic,.heif">
            <button class="btn btn-sm btn-primary" type="submit">Upload</button>
        </form>
    </div>
@endif

@if($events->isEmpty())
    <div class="text-muted small">No status history yet.</div>
@else
    <div class="list-group">
        @foreach($events as $ev)
            <div class="list-group-item">
                <div class="d-flex justify-content-between gap-2 flex-wrap">
                    <div class="fw-semibold text-capitalize">
                        {{ str_replace('_',' ', $ev->event_type) }}
                        <span class="text-muted fw-normal">
                            ({{ ucfirst($ev->from_status ?: '—') }} → {{ ucfirst($ev->to_status) }})
                        </span>
                    </div>
                    <div class="text-muted small">
                        Effective: {{ $ev->effective_at?->format('Y-m-d H:i') ?? '—' }}
                    </div>
                </div>
                @if($ev->reason)
                    <div class="mt-1 small">{!! nl2br(e($ev->reason)) !!}</div>
                @endif
                <div class="mt-1 text-muted small">
                    By: {{ $ev->creator?->name ?: '—' }} · Logged: {{ $ev->created_at?->format('Y-m-d H:i') ?? '—' }}
                </div>

                @php
                    $attachments = collect($ev->attachments ?? []);
                    $disk = \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'));
                @endphp
                @if($attachments->isNotEmpty())
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        @foreach($attachments as $a)
                            @php
                                $path = data_get($a,'path');
                                $label = data_get($a,'type') === 'death_certificate' ? 'Death Certificate' : (data_get($a,'original') ?: 'Attachment');
                                $url = null;
                                if($path){
                                    try {
                                        $url = config('filesystems.default')==='s3'
                                            ? $disk->temporaryUrl($path, now()->addMinutes(5))
                                            : $disk->url($path);
                                    } catch (\Throwable $e) { $url = null; }
                                }
                            @endphp
                            @if($url)
                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $url }}">
                                    <span class="bi bi-paperclip me-1"></span>{{ $label }}
                                </a>
                            @else
                                <span class="text-muted small">(missing attachment)</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
