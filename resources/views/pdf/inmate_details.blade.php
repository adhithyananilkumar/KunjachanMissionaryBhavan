<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inmate Details - {{ $inmate->admission_number }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.4; color: #333; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0f4f4b; padding-bottom: 10px; }
        .header h1 { color: #0f4f4b; margin: 5px 0; font-size: 20px; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 14px; font-weight: bold; color: #0f4f4b; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-bottom: 8px; text-transform: uppercase; }
        .row { margin-bottom: 5px; }
        .label { font-weight: bold; width: 130px; display: inline-block; color: #555; }
        .value { display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 5px; }
        .page-number:after { content: counter(page); }
        .photo { float: right; width: 100px; height: 100px; border: 1px solid #ddd; padding: 2px; margin-left: 10px; }
        .photo img { width: 100%; height: 100%; object-fit: cover; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Kunjachan Missionary Bhavan') }}</h1>
        <p>Inmate Profile: {{ $inmate->full_name }}</p>
    </div>

    <div class="section clearfix">
        @if($inmate->photo_path)
            <div class="photo">
                {{-- Use absolute path for PDF generation --}}
                <img src="{{ storage_path('app/public/' . str_replace('public/', '', $inmate->photo_path)) }}" alt="Photo">
            </div>
        @endif
        
        <div class="section-title">Basic Information</div>
        <div class="row"><span class="label">Admission Number:</span> <span class="value">{{ $inmate->admission_number }}</span></div>
        <div class="row"><span class="label">Registration Number:</span> <span class="value">{{ $inmate->registration_number ?: '—' }}</span></div>
        <div class="row"><span class="label">Admission Date:</span> <span class="value">{{ $inmate->admission_date ? $inmate->admission_date->format('d-M-Y') : '—' }}</span></div>
        <div class="row"><span class="label">Institution:</span> <span class="value">{{ $inmate->institution?->name ?: '—' }}</span></div>
        <div class="row"><span class="label">Type:</span> <span class="value">{{ ucfirst(str_replace('_', ' ', $inmate->type)) }}</span></div>
        <div class="row"><span class="label">Gender:</span> <span class="value">{{ $inmate->gender }}</span></div>
        <div class="row"><span class="label">Date of Birth:</span> <span class="value">{{ $inmate->date_of_birth ? $inmate->date_of_birth->format('d-M-Y') : '—' }} (Age: {{ $inmate->age ?? '—' }})</span></div>
        <div class="row"><span class="label">Blood Group:</span> <span class="value">{{ $inmate->blood_group ?: '—' }}</span></div>
    </div>

    <div class="section">
        <div class="section-title">Personal Details</div>
        <div class="row"><span class="label">Marital Status:</span> <span class="value">{{ $inmate->marital_status ?: '—' }}</span></div>
        <div class="row"><span class="label">Religion:</span> <span class="value">{{ $inmate->religion ?: '—' }}</span></div>
        <div class="row"><span class="label">Caste:</span> <span class="value">{{ $inmate->caste ?: '—' }}</span></div>
        <div class="row"><span class="label">Nationality:</span> <span class="value">{{ $inmate->nationality ?: '—' }}</span></div>
        <div class="row"><span class="label">Aadhaar Number:</span> <span class="value">{{ $inmate->aadhaar_number ?: '—' }}</span></div>
        <div class="row"><span class="label">Identification Marks:</span> <span class="value">{{ $inmate->identification_marks ?: '—' }}</span></div>
    </div>

    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="row"><span class="label">Address:</span> <span class="value">
            @if(is_array($inmate->address))
                {{ implode(', ', array_filter($inmate->address)) }}
            @else
                {{ $inmate->address ?: '—' }}
            @endif
        </span></div>
        <div class="row"><span class="label">Father's Name:</span> <span class="value">{{ $inmate->father_name ?: '—' }}</span></div>
        <div class="row"><span class="label">Mother's Name:</span> <span class="value">{{ $inmate->mother_name ?: '—' }}</span></div>
        <div class="row"><span class="label">Spouse's Name:</span> <span class="value">{{ $inmate->spouse_name ?: '—' }}</span></div>
    </div>

    @if($inmate->guardian_name)
    <div class="section">
        <div class="section-title">Guardian Details</div>
        <div class="row"><span class="label">Name:</span> <span class="value">{{ $inmate->guardian_name }}</span></div>
        <div class="row"><span class="label">Relation:</span> <span class="value">{{ $inmate->guardian_relation ?: '—' }}</span></div>
        <div class="row"><span class="label">Phone:</span> <span class="value">{{ $inmate->guardian_phone ?: '—' }}</span></div>
        <div class="row"><span class="label">Email:</span> <span class="value">{{ $inmate->guardian_email ?: '—' }}</span></div>
        <div class="row"><span class="label">Address:</span> <span class="value">{{ $inmate->guardian_address ?: '—' }}</span></div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Medical & Care</div>
        <div class="row"><span class="label">Height:</span> <span class="value">{{ $inmate->height ? $inmate->height . ' cm' : '—' }}</span></div>
        <div class="row"><span class="label">Weight:</span> <span class="value">{{ $inmate->weight ? $inmate->weight . ' kg' : '—' }}</span></div>
        
        @if($inmate->type === 'elderly' && $inmate->geriatricCarePlan)
            <div class="row"><span class="label">Mobility:</span> <span class="value">{{ $inmate->geriatricCarePlan->mobility_status ?: '—' }}</span></div>
            <div class="row"><span class="label">Dietary Needs:</span> <span class="value">{{ $inmate->geriatricCarePlan->dietary_needs ?: '—' }}</span></div>
        @endif
        
        @if($inmate->type === 'mental_health' && $inmate->mentalHealthPlan)
            <div class="row"><span class="label">Diagnosis:</span> <span class="value">{{ $inmate->mentalHealthPlan->diagnosis ?: '—' }}</span></div>
            <div class="row"><span class="label">Current Meds:</span> <span class="value">{{ $inmate->mentalHealthPlan->current_meds ?: '—' }}</span></div>
        @endif

        @if(!empty($inmate->health_info))
            <div class="row"><span class="label">Health Notes:</span> <span class="value">
                @if(is_array($inmate->health_info))
                    {{ $inmate->health_info['notes'] ?? '—' }}
                @else
                    {{ $inmate->health_info }}
                @endif
            </span></div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Allocation</div>
        <div class="row"><span class="label">Current Location:</span> <span class="value">{{ optional($inmate->currentLocation?->location)->name ?? 'Not assigned' }}</span></div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d-M-Y H:i') }} | Page <span class="page-number"></span>
    </div>
</body>
</html>
