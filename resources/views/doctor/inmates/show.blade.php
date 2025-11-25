<x-app-layout>
        <x-slot name="header">
                @push('styles')
                <style>
                /* Page-scoped mobile tweaks */
                @media (max-width: 576px){
                    .doctor-inmate-header .actions{ width:100%; display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.5rem; }
                    .doctor-inmate-header .actions .btn{ flex:0 0 auto; }
                    #inmateTabs .nav-link{ padding:.25rem .5rem; }
                    .tab-content .table{ font-size:.98rem; }
                    .doctor-inmate-header h2{ font-size:1.05rem; }
                    .doctor-inmate-header img{ width:48px; height:48px; }
                    /* Better mobile visibility */
                    .mobile-card{ background:#fff; border-radius:.75rem; box-shadow:0 2px 10px rgba(16,24,40,.08); padding:.75rem; }
                    .mobile-chip{ display:inline-block; background:#eef2ff; color:#3730a3; border-radius:9999px; padding:.15rem .6rem; font-size:.8rem; }
                }
                .tab-content .table-responsive{ overflow-x:auto; }
                .text-wrap{ word-wrap:break-word; overflow-wrap:anywhere; }
                </style>
                @endpush
                <div class="doctor-inmate-header d-flex align-items-center gap-3 flex-wrap">
            <img src="{{ $inmate->avatar_url }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;" alt="avatar">
            <div>
                <h2 class="h5 mb-1 d-flex align-items-center gap-2">
                    {{ $inmate->full_name }}
                    @if($inmate->registration_number)
                        <span class="badge text-bg-secondary ms-2">#{{ $inmate->registration_number }}</span>
                    @endif
                </h2>
                                <div class="small text-muted d-flex flex-wrap gap-2">
                    @if($inmate->date_of_birth)
                        @php $ageYears = $inmate->date_of_birth->age; @endphp
                        <span class="mobile-chip" title="DOB: {{ $inmate->date_of_birth?->format('Y-m-d') }}">Age: <strong>{{ $ageYears }}</strong></span>
                    @endif
                                        <span class="mobile-chip" title="Current Allocation">Alloc: <strong>{{ $inmate->currentLocation?->location?->number ?? '—' }}</strong></span>
                                </div>
            </div>
            <div class="actions ms-auto d-flex align-items-center gap-2">
                <a href="{{ route('doctor.lab-tests.create',$inmate) }}" class="btn btn-outline-primary d-none d-sm-inline-flex"><span class="bi bi-beaker me-1"></span> Order Lab Test</a>
                <a href="{{ route('doctor.inmates.index') }}" class="btn btn-link btn-sm text-decoration-none"><span class="bi bi-arrow-left me-1"></span> Back to Patients</a>
            </div>
        </div>
        @if($inmate->critical_alert)
            <div class="alert alert-danger d-flex align-items-start gap-2 shadow-sm mt-3 mb-0">
                <span class="bi bi-exclamation-triangle-fill fs-5"></span>
                <div>
                    <strong>Critical Alert:</strong>
                    <div class="mt-1">{!! nl2br(e($inmate->critical_alert)) !!}</div>
                </div>
            </div>
        @endif
    </x-slot>

    <ul class="nav nav-pills small flex-wrap gap-2" id="inmateTabs" role="tablist">
        <li class="nav-item" role="presentation">
        <button class="nav-link active" id="medical-history-tab" data-bs-toggle="tab" data-bs-target="#medical-history" type="button" role="tab"><span class="bi bi-clipboard2-pulse me-1"></span> Medical History</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="medications-tab" data-bs-toggle="tab" data-bs-target="#medications" type="button" role="tab"><span class="bi bi-capsule-pill me-1"></span> Medications</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="lab-tests-tab" data-bs-toggle="tab" data-bs-target="#lab-tests" type="button" role="tab"><span class="bi bi-beaker me-1"></span> Lab Tests</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reportings-tab" data-bs-toggle="tab" data-bs-target="#reportings" type="button" role="tab"><span class="bi bi-flag me-1"></span> Reportings</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="care-plans-tab" data-bs-toggle="tab" data-bs-target="#care-plans" type="button" role="tab"><span class="bi bi-journal-medical me-1"></span> Care Plans</button>
        </li>
    </ul>
    <div class="tab-content border rounded bg-white p-2 p-md-4 mt-2" id="inmateTabsContent">
        <div class="tab-pane fade show active" id="medical-history" role="tabpanel" aria-labelledby="medical-history-tab">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="mb-0">All Medical Records</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRecordModal"><span class="bi bi-plus-lg me-1"></span> Add New Medical Record</button>
                </div>
            </div>
            <div id="medical-history-list">
                @include('partials.inmates._medical_history', ['inmate'=>$inmate])
            </div>
        </div>

                <div class="tab-pane fade" id="medications" role="tabpanel" aria-labelledby="medications-tab">
                        @php
                            $activeMeds = $inmate->medications()->where('status','active')->orderBy('name')->get();
                            $historyMeds = $inmate->medications()
                                ->whereIn('status',["stopped","completed","paused"])
                                ->orderByDesc('end_date')
                                ->orderBy('name')
                                ->get();
                        @endphp
                        <ul class="nav nav-tabs small flex-wrap gap-2 d-none d-sm-flex" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#meds-active" type="button" role="tab">Active</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#meds-history" type="button" role="tab">History</button></li>
                        </ul>
                        <div class="d-sm-none mb-2">
                            <select class="form-select form-select-sm" id="medsTabSelect" aria-label="Select medications tab">
                                <option value="#meds-active" selected>Active</option>
                                <option value="#meds-history">History</option>
                            </select>
                        </div>
                        <div class="tab-content border-start border-end border-bottom rounded-bottom p-2 mt-0">
                            <div class="tab-pane fade show active" id="meds-active" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Current Medications</h6>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicationModal"><span class="bi bi-plus-lg me-1"></span>Add Medication</button>
                                </div>
                                <div class="table-responsive">
                                        <table class="table table-sm align-middle" id="medicationsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th class="d-none d-md-table-cell">Dosage</th>
                                                        <th class="d-none d-md-table-cell">Route</th>
                                                        <th class="d-none d-md-table-cell">Frequency</th>
                                                        <th class="d-none d-md-table-cell">Dates</th>
                                                        <th class="d-none d-md-table-cell"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @forelse(($activeMeds ?? []) as $m)
                                                    <tr data-id="{{ $m->id }}">
                                                        <td class="fw-semibold">
                                                            <button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#med-det-{{ $m->id }}">
                                                                <span class="med-name">{{ $m->name }}</span>
                                                                <span class="bi bi-chevron-down ms-1 small"></span>
                                                            </button>
                                                        </td>
                                                        <td class="med-dosage d-none d-md-table-cell">{{ $m->dosage ?: '—' }}</td>
                                                        <td class="med-route d-none d-md-table-cell">{{ $m->route ?: '—' }}</td>
                                                        <td class="med-frequency d-none d-md-table-cell">{{ $m->frequency ?: '—' }}</td>
                                                        <td class="med-dates d-none d-md-table-cell">{{ $m->start_date ?: '—' }} @if($m->end_date) - {{ $m->end_date }} @endif</td>
                                                        <td class="text-end d-none d-md-table-cell">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" type="button">Actions</button>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <button class="dropdown-item edit-med" type="button">Edit</button>
                                                                    <button class="dropdown-item text-danger stop-med" type="button">Stop</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="d-md-none">
                                                        <td colspan="6" class="pt-0">
                                                            <div class="collapse" id="med-det-{{ $m->id }}">
                                                                <div class="mobile-card small">
                                                                    <div class="mb-1"><span class="text-muted">Dosage:</span> {{ $m->dosage ?: '—' }}</div>
                                                                    <div class="mb-1"><span class="text-muted">Route:</span> {{ $m->route ?: '—' }}</div>
                                                                    <div class="mb-1"><span class="text-muted">Frequency:</span> {{ $m->frequency ?: '—' }}</div>
                                                                    <div class="mb-2"><span class="text-muted">Dates:</span> {{ $m->start_date ?: '—' }} @if($m->end_date) - {{ $m->end_date }} @endif</div>
                                                                    <div class="mt-2 d-flex gap-2">
                                                                        <button class="btn btn-sm btn-outline-secondary edit-med" type="button"
                                                                                data-id="{{ $m->id }}" data-name="{{ e($m->name) }}" data-dosage="{{ e($m->dosage) }}" data-route="{{ e($m->route) }}" data-frequency="{{ e($m->frequency) }}">Edit</button>
                                                                        <button class="btn btn-sm btn-outline-danger stop-med" type="button" data-id="{{ $m->id }}">Stop</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6" class="text-center py-4 text-muted">No active medications.</td></tr>
                                                @endforelse
                                                </tbody>
                                        </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="meds-history" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th class="d-none d-md-table-cell">Details</th>
                                                <th class="d-none d-sm-table-cell">Start</th>
                                                <th class="d-none d-sm-table-cell">End</th>
                                                <th class="d-none d-sm-table-cell">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse(($historyMeds ?? []) as $m)
                                            <tr>
                                                <td class="fw-semibold text-wrap">
                                                    <button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#medhist-{{ $m->id }}">{{ $m->name }}<span class="bi bi-chevron-down ms-1 small"></span></button>
                                                </td>
                                                <td class="d-none d-md-table-cell">{{ $m->dosage ?: '—' }} {{ $m->route ? ' · '.$m->route : '' }} {{ $m->frequency ? ' · '.$m->frequency : '' }}</td>
                                                <td class="d-none d-sm-table-cell">{{ $m->start_date ?: '—' }}</td>
                                                <td class="d-none d-sm-table-cell">{{ $m->end_date ?: '—' }}</td>
                                                <td class="d-none d-sm-table-cell text-capitalize">{{ $m->status }}</td>
                                            </tr>
                                            <tr class="d-md-none">
                                                <td colspan="5" class="pt-0">
                                                    <div class="collapse" id="medhist-{{ $m->id }}">
                                                        <div class="border rounded p-2 bg-light-subtle small">
                                                            <div><span class="text-muted">Dosage:</span> {{ $m->dosage ?: '—' }}</div>
                                                            <div><span class="text-muted">Route:</span> {{ $m->route ?: '—' }}</div>
                                                            <div><span class="text-muted">Frequency:</span> {{ $m->frequency ?: '—' }}</div>
                                                            <div class="d-sm-none"><span class="text-muted">Start:</span> {{ $m->start_date ?: '—' }}</div>
                                                            <div class="d-sm-none"><span class="text-muted">End:</span> {{ $m->end_date ?: '—' }}</div>
                                                            <div class="d-sm-none"><span class="text-muted">Status:</span> <span class="text-capitalize">{{ $m->status }}</span></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center py-4 text-muted">No medication history.</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>

        <div class="tab-pane fade" id="lab-tests" role="tabpanel" aria-labelledby="lab-tests-tab">
            @php $labTests = $inmate->labTests()->latest()->get(); @endphp
        <div class="table-responsive d-none d-md-block">
                <table class="table table-sm align-middle">
            <thead class="table-light"><tr><th>Test & Dates</th><th>Ordered</th><th>Completed</th><th>Report</th><th>Notes</th><th>Action</th></tr></thead>
                    <tbody>
                    @forelse($labTests as $lt)
                        <tr>
                            <td class="fw-semibold">
                                <span class="bi bi-beaker me-1"></span>
                <a href="{{ route('doctor.lab-tests.show', $lt) }}" class="text-decoration-none">{{ $lt->test_name }}</a>
                <div class="small text-muted">Ordered: {{ optional($lt->ordered_date)->format('Y-m-d') ?: '—' }} @if($lt->completed_date) · Completed: {{ optional($lt->completed_date)->format('Y-m-d') }} @endif</div>
                            </td>
                            
                            <td>{{ optional($lt->ordered_date)->format('Y-m-d') }}</td>
                            <td>{{ optional($lt->completed_date)->format('Y-m-d') ?: '—' }}</td>
                            <td>
                                @if($lt->result_file_path)
                                    @php $disk = Storage::disk(config('filesystems.default')); $rurl = $lt->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($lt->result_file_path, now()->addMinutes(5)) : $disk->url($lt->result_file_path)) : null; @endphp
                                    @if($rurl)<a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $rurl }}"><span class="bi bi-file-earmark-pdf me-1"></span>View</a>@endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                                                        <td>
                                                                @if($lt->result_notes)
                                                                     <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#labNotesModal" data-notes="{{ e($lt->result_notes) }}" data-test="{{ e($lt->test_name) }}"><span class="bi bi-card-text me-1"></span>Notes</button>
                                                                @else
                                                                     <span class="text-muted">—</span>
                                                                @endif
                                                        </td>
                                                        <td class="d-flex flex-wrap gap-1">
                                                            <a href="{{ route('doctor.lab-tests.show', $lt) }}" class="btn btn-sm btn-outline-secondary"><span class="bi bi-eye me-1"></span>Details</a>
                                                                @if(!$lt->reviewed_at && in_array($lt->status,['completed']))
                                                                        <form method="POST" action="{{ route('doctor.lab-tests.update',$lt) }}" onsubmit="return confirm('Accept and lock this report? This action cannot be undone.');">
                                                                                @csrf
                                                                                @method('PUT')
                                                                    <input type="hidden" name="accept" value="1">
                                                                                <input type="hidden" name="status" value="{{ $lt->status }}">
                                                                                <input type="hidden" name="result_notes" value="{{ $lt->result_notes }}">
                                                                                <button class="btn btn-sm btn-success"><span class="bi bi-check2-circle me-1"></span>Accept</button>
                                                                        </form>
                                                                @endif
                                                        </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No lab tests.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
                        <!-- Mobile accordion for Lab Tests -->
                        <div class="d-md-none">
                            <div class="accordion" id="labTestsAcc">
                                @forelse($labTests as $lt)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="lab-h-{{ $lt->id }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lab-c-{{ $lt->id }}">
                                                <span class="bi bi-beaker me-2"></span>{{ $lt->test_name }}
                                                
                                                <div class="w-100 small text-muted mt-1">Ordered: {{ optional($lt->ordered_date)->format('Y-m-d') ?: '—' }} @if($lt->completed_date) · Completed: {{ optional($lt->completed_date)->format('Y-m-d') }} @endif</div>
                                            </button>
                                        </h2>
                                        <div id="lab-c-{{ $lt->id }}" class="accordion-collapse collapse" data-bs-parent="#labTestsAcc">
                                            <div class="accordion-body">
                                                <div class="small text-muted">Ordered: {{ optional($lt->ordered_date)->format('Y-m-d') ?: '—' }}</div>
                                                <div class="small text-muted mb-2">Completed: {{ optional($lt->completed_date)->format('Y-m-d') ?: '—' }}</div>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('doctor.lab-tests.show', $lt) }}" class="btn btn-sm btn-outline-secondary"><span class="bi bi-eye me-1"></span>Details</a>
                                                    @if($lt->result_file_path)
                                                        @php $rurl = $lt->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($lt->result_file_path, now()->addMinutes(5)) : $disk->url($lt->result_file_path)) : null; @endphp
                                                        @if($rurl)<a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $rurl }}"><span class="bi bi-file-earmark-pdf me-1"></span>Report</a>@endif
                                                    @endif
                                                    @if($lt->result_notes)
                                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#labNotesModal" data-notes="{{ e($lt->result_notes) }}" data-test="{{ e($lt->test_name) }}"><span class="bi bi-card-text me-1"></span>Notes</button>
                                                    @endif
                                                    <a href="{{ route('doctor.inmates.show',$inmate) }}?addRecord=1&lab_test={{ $lt->id }}" class="btn btn-sm btn-outline-primary"><span class="bi bi-clipboard-plus me-1"></span>Add Medical Record</a>
                                                    @if(!$lt->reviewed_at && in_array($lt->status,['completed']))
                                                        <form method="POST" action="{{ route('doctor.lab-tests.update',$lt) }}" onsubmit="return confirm('Accept and lock this report?');" class="d-inline">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="status" value="{{ $lt->status }}">
                                                            <button class="btn btn-sm btn-success"><span class="bi bi-check2-circle me-1"></span>Accept</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">No lab tests.</div>
                                @endforelse
                            </div>
                        </div>
        </div>

        <div class="tab-pane fade" id="reportings" role="tabpanel" aria-labelledby="reportings-tab">
            @php $exams = $inmate->examinations()->limit(50)->get(); @endphp
            @if($exams->isEmpty())
                <div class="text-muted small">No reportings yet.</div>
            @else
                <div class="list-group">
                    @foreach($exams as $ex)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                <div>
                                    <div class="fw-semibold">
                                        {{ $ex->title ?: 'Reporting' }}
                                        @if($ex->severity)
                                            <span class="badge bg-light text-dark border ms-1 text-capitalize">{{ $ex->severity }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">{{ $ex->observed_at?->format('Y-m-d H:i') ?? $ex->created_at->format('Y-m-d H:i') }} • {{ ucfirst($ex->creator_role) }}: {{ $ex->creator?->name }}</div>
                                </div>
                            </div>
                            <div class="mt-2 small text-wrap">{{ $ex->notes }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    <div class="tab-pane fade" id="care-plans" role="tabpanel" aria-labelledby="care-plans-tab">
            @if($inmate->type === 'rehabilitation_patient')
                @include('partials.inmates._therapy_logs',['inmate'=>$inmate])
                @include('partials.inmates._rehabilitation_plan')
            @endif
            @if($inmate->type === 'mental_health_patient')
                @include('partials.inmates._mental_health_plan')
            @endif
            @if($inmate->type === 'geriatric_patient')
                @include('partials.inmates._geriatric_care')
            @endif
            {{-- Doctors view focuses on health; educational/intake hidden here intentionally --}}
        </div>
    </div>

        <!-- Add Medical Record Modal -->
                            <style>
                                /* Scope to Add Medical Record modal */
                                #addRecordModal { overflow-x: hidden; }
                                #addRecordModal .modal-dialog { max-width: min(900px, 95vw); margin: .75rem auto; }
                                /* Ensure the modal content fits the viewport and the body scrolls */
                                #addRecordModal .modal-content { overflow-x: hidden; display: flex; flex-direction: column; max-height: min(92vh, 100dvh); min-height: 60vh; }
                                #addRecordModal .modal-header { position: sticky; top: 0; background: var(--bs-body-bg); z-index: 2; }
                                #addRecordModal .modal-body { overflow-x: hidden; overflow-y: auto; -webkit-overflow-scrolling: touch; overscroll-behavior: contain; flex: 1 1 auto; min-height: 0; padding-bottom: 5rem; max-height: calc(100dvh - 8rem); }
                                /* Keep actions visible at the bottom while content scrolls */
                                #addRecordModal .modal-footer { position: sticky; bottom: 0; background: var(--bs-body-bg); box-shadow: 0 -4px 12px rgba(0,0,0,.06); z-index: 2; }
                                #addRecordModal .form-control { min-width: 0; }
                                #addRecordModal .border.rounded { overflow: hidden; }
                                #addRecordModal textarea { resize: vertical; min-height: 3rem; }
                                @media (max-width: 576px){
                                    #addRecordModal .modal-dialog { margin: .5rem auto; }
                                }
                            </style>
    <div class="modal fade" id="addRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="bi bi-clipboard2-plus me-1"></span> Add Medical Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                                <form id="medicalRecordForm" method="POST" action="{{ route('doctor.inmates.medical-records.store', $inmate) }}">
                    @csrf
                                        @if(request('lab_test'))
                                            <input type="hidden" name="lab_test_id" value="{{ request('lab_test') }}">
                                        @endif
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prescription (optional)</label>
                            <textarea name="prescription" class="form-control" rows="2" placeholder="Optional notes"></textarea>
                        </div>
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Medications (structured)</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="add-med-btn"><span class="bi bi-plus-lg me-1"></span> Add Medication</button>
                            </div>
                            <div id="medications-wrapper"></div>
                        </div>
                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0">Next appointment</h6>
                                <div class="form-check form-switch">
                                  <input class="form-check-input" type="checkbox" id="nextApptToggle">
                                  <label class="form-check-label small" for="nextApptToggle">Schedule after saving</label>
                                </div>
                            </div>
                            <small class="text-muted d-block mb-2">We'll create the appointment after the record is saved.</small>
                            <div id="nextApptFields" class="row g-2 d-none">
                                <div class="col-md-6">
                                    <label class="form-label small">Date</label>
                                    <input type="date" class="form-control" id="nextApptDate">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Title</label>
                                    <input type="text" class="form-control" id="nextApptTitle" placeholder="Follow-up">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Notes (optional)</label>
                                    <input type="text" class="form-control" id="nextApptNotes" placeholder="e.g., Review diagnosis / labs">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><span class="bi bi-save me-1"></span> Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Medication Modal -->
    <div class="modal fade" id="addMedicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Medication</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="addMedicationForm" action="{{ route('doctor.inmates.medications.store',$inmate) }}" method="POST">@csrf
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6"><label class="form-label small">Name</label><input name="name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label small">Dosage</label><input name="dosage" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small">Route</label><input name="route" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small">Frequency</label><input name="frequency" class="form-control" placeholder="1/0/1"></div>
                            <div class="col-md-4"><label class="form-label small">Start Date</label><input type="date" name="start_date" class="form-control" placeholder="yyyy-mm-dd"></div>
                            <div class="col-md-12"><label class="form-label small">Instructions</label><textarea name="instructions" rows="2" class="form-control"></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-primary" id="addMedicationSubmit"><span class="bi bi-save me-1"></span>Save Medication</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Medication Modal -->
    <div class="modal fade" id="editMedicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Medication</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="editMedicationForm" method="POST">@csrf @method('PUT')
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6"><label class="form-label small">Name</label><input name="name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label small">Dosage</label><input name="dosage" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small">Route</label><input name="route" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small">Frequency</label><input name="frequency" class="form-control" placeholder="1/0/1"></div>
                            <div class="col-md-4"><label class="form-label small">Start Date</label><input type="date" name="start_date" class="form-control" placeholder="yyyy-mm-dd"></div>
                            <div class="col-md-4"><label class="form-label small">End Date</label><input type="date" name="end_date" class="form-control"></div>
                            <div class="col-md-12"><label class="form-label small">Instructions</label><textarea name="instructions" rows="2" class="form-control"></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-danger" id="stopMedicationBtn"><span class="bi bi-x-octagon me-1"></span>Stop</button>
                        <div>
                            <button class="btn btn-primary"><span class="bi bi-save me-1"></span>Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lab Notes Modal -->
    <div class="modal fade" id="labNotesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="labNotesTitle">Lab Result Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="labNotesBody" class="mb-0" style="white-space:pre-wrap; font-family: var(--bs-font-sans-serif);"></pre>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  // dynamic medications rows
  const wrapper=document.getElementById('medications-wrapper');
  const btn=document.getElementById('add-med-btn');
    let i=0; function row(){
        const div=document.createElement('div');
        div.className='border rounded p-2 mb-2 bg-light-subtle';
        div.innerHTML=`
        <div class="row g-2 align-items-end mx-0">
            <div class="col-12 col-md-3">
                <label class="form-label small">Name</label>
                <input name="med_name[${i}]" type="text" class="form-control" required>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Dosage</label>
                <input name="med_dosage[${i}]" type="text" class="form-control">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Route</label>
                <input name="med_route[${i}]" type="text" class="form-control" placeholder="oral">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Frequency</label>
                <input name="med_frequency[${i}]" type="text" class="form-control" placeholder="1/0/1">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small">Start / End</label>
            <div class="row g-1 mx-0">
                    <div class="col-6"><input name="med_start_date[${i}]" type="date" class="form-control"></div>
                    <div class="col-6"><input name="med_end_date[${i}]" type="date" class="form-control"></div>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <label class="form-label small mb-1">Instructions</label>
            <textarea name="med_instructions[${i}]" rows="2" class="form-control" placeholder="Optional"></textarea>
        </div>
        <div class="text-end mt-1">
            <button type="button" class="btn btn-sm btn-outline-danger remove-med"><span class="bi bi-trash me-1"></span>Remove</button>
        </div>`;
        wrapper.appendChild(div); i++; }
  if(btn){ btn.addEventListener('click', ()=> row()); }
  wrapper?.addEventListener('click', e=>{ if(e.target.closest('.remove-med')) e.target.closest('.border').remove(); });

    // Next appointment toggle defaults
    const apptToggle = document.getElementById('nextApptToggle');
    const apptFields = document.getElementById('nextApptFields');
    const apptDate = document.getElementById('nextApptDate');
    const apptTitle = document.getElementById('nextApptTitle');
    if(apptToggle){
            apptToggle.addEventListener('change', ()=>{ apptFields?.classList.toggle('d-none', !apptToggle.checked); });
            // default to 7 days from now and title
            const d = new Date(); d.setDate(d.getDate()+7); apptDate.value = d.toISOString().slice(0,10);
            if(apptTitle && !apptTitle.value) apptTitle.value = 'Follow-up';
    }

  // AJAX submit
  const form = document.getElementById('medicalRecordForm');
  form?.addEventListener('submit', function(e){
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    $.ajax({
        url: form.action,
        method: 'POST',
        data: $(form).serialize(),
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        success: function(resp){
            toastr.success('Medical record saved');
            // Optionally create a next appointment
            const makeAppt = apptToggle?.checked;
            const makeApptDate = document.getElementById('nextApptDate')?.value;
            const makeApptTitle = document.getElementById('nextApptTitle')?.value || 'Follow-up';
            const makeApptNotes = document.getElementById('nextApptNotes')?.value || '';

            const afterCleanup = ()=>{
                const modalEl = document.getElementById('addRecordModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                form.reset(); wrapper.innerHTML=''; i=0;
                if(apptToggle){ apptToggle.checked=false; apptFields?.classList.add('d-none'); }
            };

            if(makeAppt && makeApptDate){
                fetch('{{ route('doctor.appointments.store') }}', {
                    method:'POST',
                    headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept':'application/json','Content-Type':'application/json'},
                    body: JSON.stringify({
                        inmate_id: {{ $inmate->id }},
                        title: makeApptTitle,
                        scheduled_for: makeApptDate,
                        notes: makeApptNotes
                    })
                })
                .then(r=>r.json().then(data=>({ok:r.ok,data})))
                .then(({ok,data})=>{
                    if(!ok){ throw new Error(data?.message || 'Appointment could not be created'); }
                    toastr.success('Next appointment scheduled for '+ makeApptDate);
                })
                .catch(err=>{ toastr.error(err.message || 'Failed to schedule next appointment'); })
                .finally(afterCleanup);
            } else {
                afterCleanup();
            }
            // refresh medical history list
            $('#medical-history-list').load(location.href + ' #medical-history-list>*','');
        },
        error: function(xhr){
            const msg = xhr.responseJSON?.message || 'Failed to save record';
            toastr.error(msg);
        },
        complete: function(){ submitBtn.disabled=false; submitBtn.innerHTML = '<span class="bi bi-save me-1"></span> Save Record'; }
    });
  });

    // When the modal opens, auto-add first medication row and focus name
    const addRecordModal = document.getElementById('addRecordModal');
    if(addRecordModal){
        addRecordModal.addEventListener('shown.bs.modal', ()=>{
            try{
                const hasRow = wrapper && wrapper.querySelector('.border');
                if(!hasRow){
                    document.getElementById('add-med-btn')?.click();
                }
                // small delay to let DOM insert
                setTimeout(()=> wrapper?.querySelector('input[name^="med_name"]')?.focus(), 50);
            }catch(e){}
        });
    }

    // Add Medication AJAX
    const addMedForm = document.getElementById('addMedicationForm');
    addMedForm?.addEventListener('submit', function(e){
        e.preventDefault();
        $.post(addMedForm.action, $(addMedForm).serialize())
            .done(resp=>{
                toastr.success('Medication added');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addMedicationModal')); modal.hide();
                // refresh active meds tab
                $('#meds-active').load(location.href + ' #meds-active>*','');
            })
            .fail(xhr=>{ toastr.error(xhr.responseJSON?.message || 'Failed'); });
    });

    // Edit Medication fill and submit
    document.addEventListener('click', function(e){
        const btn = e.target.closest('#medicationsTable .edit-med, #medications .edit-med'); if(!btn) return;
        const tr = btn.closest('tr');
        // Resolve medication id from button dataset, current row, or the previous header row
        const id = btn.dataset.id || tr?.dataset?.id || tr?.previousElementSibling?.dataset?.id;
        if(!id){ console.warn('Missing medication id for edit'); return; }
        const headerRow = (tr && tr.dataset && tr.dataset.id) ? tr : tr?.previousElementSibling; // row that holds visible cells

        const modalEl = document.getElementById('editMedicationModal');
        const form = document.getElementById('editMedicationForm');
        form.action = '{{ url('doctor/medications') }}/'+id;
        // Prefill using dataset when available; fallback to header row cells
        const getCellTxt = (sel)=> headerRow?.querySelector(sel)?.textContent?.trim() || '';
        form.name.value = btn.dataset.name || getCellTxt('.med-name');
        const dosageTxt = btn.dataset.dosage || getCellTxt('.med-dosage');
        const routeTxt = btn.dataset.route || getCellTxt('.med-route');
        const freqTxt = btn.dataset.frequency || getCellTxt('.med-frequency');
        form.dosage.value = (dosageTxt && dosageTxt !== '—') ? dosageTxt : '';
        form.route.value = (routeTxt && routeTxt !== '—') ? routeTxt : '';
        form.frequency.value = (freqTxt && freqTxt !== '—') ? freqTxt : '';
        // end/start dates not in separate cells; skip prefill end
        new bootstrap.Modal(modalEl).show();
    });
    document.getElementById('editMedicationForm')?.addEventListener('submit', function(e){
        e.preventDefault(); const form=this;
        $.ajax({url: form.action, method:'POST', data: $(form).serialize(), headers:{'X-HTTP-Method-Override':'PUT'}})
            .done(()=>{ toastr.success('Medication updated'); bootstrap.Modal.getInstance(document.getElementById('editMedicationModal')).hide(); $('#meds-active').load(location.href + ' #meds-active>*',''); })
            .fail(xhr=> toastr.error(xhr.responseJSON?.message || 'Failed'));
    });
    document.getElementById('stopMedicationBtn')?.addEventListener('click', function(){
        const form = document.getElementById('editMedicationForm');
        const data = $(form).serialize() + '&status=stopped&end_date='+new Date().toISOString().slice(0,10);
        $.ajax({url: form.action, method:'POST', data: data, headers:{'X-HTTP-Method-Override':'PUT'}})
            .done(()=>{ toastr.success('Medication stopped'); bootstrap.Modal.getInstance(document.getElementById('editMedicationModal')).hide(); $('#meds-active').load(location.href + ' #meds-active>*',''); })
            .fail(xhr=> toastr.error(xhr.responseJSON?.message || 'Failed'));
    });

    // Direct stop from dropdown
    document.addEventListener('click', function(e){
        const stopBtn = e.target.closest('#medicationsTable .stop-med, #medications .stop-med'); if(!stopBtn) return;
        const tr = stopBtn.closest('tr');
        const id = stopBtn.dataset.id || tr?.dataset?.id || tr?.previousElementSibling?.dataset?.id;
        if(!id){ console.warn('Missing medication id for stop'); return; }
        const form = document.getElementById('editMedicationForm');
        form.action = '{{ url('doctor/medications') }}/'+id;
        const data = $(form).serialize() + '&status=stopped&end_date='+new Date().toISOString().slice(0,10);
        $.ajax({url: form.action, method:'POST', data: data, headers:{'X-HTTP-Method-Override':'PUT'}})
            .done(()=>{ toastr.success('Medication stopped'); $('#meds-active').load(location.href + ' #meds-active>*',''); })
            .fail(xhr=> toastr.error(xhr.responseJSON?.message || 'Failed'));
    });

    // Mobile select to switch tabs
    const medsSelect = document.getElementById('medsTabSelect');
    medsSelect?.addEventListener('change', function(){
        const pane = document.querySelector(this.value);
        if(!pane) return;
        const trigger = document.querySelector(`[data-bs-target="${this.value}"]`);
        if(trigger){
            bootstrap.Tab.getOrCreateInstance(trigger).show();
        }else{
            document.querySelectorAll('#medications .tab-pane').forEach(p=>p.classList.remove('show','active'));
            pane.classList.add('show','active');
        }
    });
});
</script>
<script>
// Make frequency input usable: quick-picks (OD/BD/TDS/HS), M/N/N toggles, and gentle formatting
document.addEventListener('DOMContentLoaded', ()=>{
    function parseToSlots(val){
        if(!val) return [0,0,0];
        const u = String(val).trim().toUpperCase();
        if(['OD','QD'].includes(u)) return [1,0,0];
        if(['BD','BID'].includes(u)) return [1,0,1];
        if(['TDS','TID'].includes(u)) return [1,1,1];
        if(u==='HS') return [0,0,1];
        // digits or a/b/c form
        const parts = u.replace(/[^0-9\/\-]/g,'').replace(/-/g,'/').split('/').filter(Boolean);
        if(parts.length>0 && parts.every(p=>/^[0-9]$/.test(p))){
            const a = parseInt(parts[0]||'0',10)>0?1:0;
            const b = parseInt(parts[1]||'0',10)>0?1:0;
            const c = parseInt(parts[2]||'0',10)>0?1:0;
            return [a,b,c];
        }
        const digits = u.replace(/[^0-9]/g,'').slice(0,3);
        return [Number(digits[0]||0)>0?1:0, Number(digits[1]||0)>0?1:0, Number(digits[2]||0)>0?1:0];
    }
    function slotsToText([a,b,c]){ return `${a}/${b}/${c}`; }

    function makeHelper(inp){
        if(inp.dataset.freqEnhanced) return; inp.dataset.freqEnhanced = '1';
        const wrap = document.createElement('div');
        wrap.className = 'd-flex align-items-center gap-2 mt-1 flex-wrap';
    const mid = `m-${Math.random().toString(36).slice(2)}`;
    const nid = `n-${Math.random().toString(36).slice(2)}`;
    const eid = `e-${Math.random().toString(36).slice(2)}`;
    wrap.innerHTML = `
            <div class="d-flex flex-wrap gap-3 align-items-start">
                <div>
                    <div class="small text-muted mb-1">Quick</div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Quick codes">
                        <button type="button" class="btn btn-outline-secondary" data-code="OD">OD</button>
                        <button type="button" class="btn btn-outline-secondary" data-code="BD">BD</button>
                        <button type="button" class="btn btn-outline-secondary" data-code="TDS">TDS</button>
                        <button type="button" class="btn btn-outline-secondary" data-code="HS">HS</button>
                    </div>
                </div>
                <div>
                    <div class="small text-muted mb-1">Times</div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Windows">
                        <input type="checkbox" class="btn-check" id="${mid}"><label class="btn btn-outline-primary" for="${mid}">Morning</label>
                        <input type="checkbox" class="btn-check" id="${nid}"><label class="btn btn-outline-primary" for="${nid}">Noon</label>
                        <input type="checkbox" class="btn-check" id="${eid}"><label class="btn btn-outline-primary" for="${eid}">Night</label>
                    </div>
                </div>
            </div>`;
    inp.insertAdjacentElement('afterend', wrap);
    const [quick, windows] = wrap.querySelectorAll('.btn-group');
    const mChk = windows.querySelector(`#${CSS.escape(mid)}`);
    const nChk = windows.querySelector(`#${CSS.escape(nid)}`);
    const eChk = windows.querySelector(`#${CSS.escape(eid)}`);

        function renderFromInput(){
            const [a,b,c] = parseToSlots(inp.value);
            mChk.checked = !!a; nChk.checked = !!b; eChk.checked = !!c;
        }
        function renderFromToggles(){
            const a = mChk.checked?1:0, b = nChk.checked?1:0, c = eChk.checked?1:0;
            inp.value = slotsToText([a,b,c]);
        }
        quick.querySelectorAll('button').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const code = btn.dataset.code;
                const slots = parseToSlots(code);
                inp.value = slotsToText(slots);
                renderFromInput();
            });
        });
        ;[mChk,nChk,eChk].forEach(ch=> ch.addEventListener('change', renderFromToggles));

        // Gentle input formatting: do not auto-pad zeros on first digit
        inp.addEventListener('input', ()=>{
            const raw = inp.value;
            const u = String(raw).toUpperCase();
            if(['OD','QD','BD','BID','TDS','TID','HS'].includes(u)) { renderFromInput(); return; }
            const digits = u.replace(/[^0-9]/g,'').slice(0,3);
            if(digits.length===0){ renderFromInput(); return; }
            if(u.includes('/') || u.includes('-')){ renderFromInput(); return; }
            // only add separators when 2 or 3 digits; keep single digit as-is
            if(digits.length===1){ inp.value = digits; }
            else if(digits.length===2){ inp.value = `${digits[0]}/${digits[1]}`; }
            else { inp.value = `${digits[0]}/${digits[1]}/${digits[2]}`; }
            renderFromInput();
        });
        // On blur, canonicalize to a/b/c (pad trailing zeros)
        inp.addEventListener('blur', ()=>{
            const [a,b,c] = parseToSlots(inp.value);
            inp.value = slotsToText([a,b,c]);
            renderFromInput();
        });
        // Initial sync
        renderFromInput();
    }

    function enhanceFrequencyInputs(scope){
        scope.querySelectorAll('input[name="frequency"], input[name^="med_frequency"]').forEach(makeHelper);
    }

    const addMed = document.getElementById('addMedicationModal');
    const editMed = document.getElementById('editMedicationModal');
    addMed && addMed.addEventListener('shown.bs.modal', ()=> enhanceFrequencyInputs(addMed));
    editMed && editMed.addEventListener('shown.bs.modal', ()=> enhanceFrequencyInputs(editMed));

    // Enhance existing structured rows and future ones
    const wrapper = document.getElementById('medications-wrapper');
    if(wrapper){ enhanceFrequencyInputs(wrapper); }
    // Observe additions
    if(wrapper && 'MutationObserver' in window){
        const mo = new MutationObserver(()=> enhanceFrequencyInputs(wrapper));
        mo.observe(wrapper, {childList:true, subtree:true});
    }
});
</script>
<!-- Rely on Bootstrap's modal-dialog-scrollable for vertical scroll; no JS height hacks needed -->
<script>
// Guard medication modals against Enter-key submits; submit on explicit click
document.addEventListener('DOMContentLoaded', ()=>{
    const addMedModal = document.getElementById('addMedicationModal');
    const addMedBtn = document.getElementById('addMedicationSubmit');
    const addMedForm = document.getElementById('addMedicationForm');
    addMedModal?.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
    addMedBtn?.addEventListener('click', function(){
        if(!addMedForm) return;
        // Ensure the submit event (with AJAX preventDefault) is fired
        if(typeof addMedForm.requestSubmit === 'function'){
            addMedForm.requestSubmit();
        } else if(window.jQuery){
            window.jQuery(addMedForm).trigger('submit');
        } else {
            // Fallback: manually dispatch submit event
            const evt = new Event('submit', {bubbles:true, cancelable:true});
            if(addMedForm.dispatchEvent(evt)){
                // If nobody prevented default, do a normal submit
                HTMLFormElement.prototype.submit.call(addMedForm);
            }
        }
    });
});
</script>
<script>
// Bind lab notes modal population
document.addEventListener('DOMContentLoaded', ()=>{
    const modalEl = document.getElementById('labNotesModal');
    modalEl?.addEventListener('show.bs.modal', (e)=>{
        const btn = e.relatedTarget; if(!btn) return;
        const notes = btn.getAttribute('data-notes') || '';
        const test = btn.getAttribute('data-test') || 'Lab Result Notes';
        document.getElementById('labNotesTitle').textContent = test + ' — Notes';
        document.getElementById('labNotesBody').textContent = notes;
    });
});
</script>
<script>
// Auto-open Add Medical Record when coming from lab test "Add Medical Record" shortcut
document.addEventListener('DOMContentLoaded', ()=>{
    const params = new URLSearchParams(window.location.search);
    if(params.get('addRecord')==='1'){
        const modalEl = document.getElementById('addRecordModal');
        if(modalEl){ new bootstrap.Modal(modalEl).show(); }
    }
});
</script>
@endpush
</x-app-layout>
