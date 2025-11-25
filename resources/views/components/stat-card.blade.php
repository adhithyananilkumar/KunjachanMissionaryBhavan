@props(['icon' => null, 'label' => '', 'value' => '—'])
<div {{ $attributes->merge(['class' => 'card stat-card shadow-sm h-100']) }}>
    <div class="card-body">
        @if($icon)
            <div class="stat-icon bi bi-{{ $icon }}"></div>
        @endif
        <div class="fw-semibold small text-uppercase text-muted">{{ $label }}</div>
        <div class="h2 fw-bold mb-0">{{ $value }}</div>
        {{ $slot }}
    </div>
</div>
