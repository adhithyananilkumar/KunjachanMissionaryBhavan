@php
    $st = strtolower((string)($status ?? data_get($inmate ?? null, 'status') ?? 'present'));
    $label = match($st){
        'present' => 'Present',
        'discharged' => 'Discharged',
        'transferred' => 'Transferred',
        'deceased' => 'Deceased',
        default => ucfirst($st),
    };
    $class = match($st){
        'present' => 'bg-success',
        'deceased' => 'bg-danger',
        'transferred' => 'bg-info text-dark',
        'discharged' => 'bg-secondary',
        default => 'bg-light text-dark border',
    };
@endphp

<span class="badge {{ $class }}">{{ $label }}</span>
