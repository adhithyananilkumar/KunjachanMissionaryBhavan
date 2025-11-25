<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Guardian Dashboard</h2></x-slot>

    @if(!$guardian)
        <div class="alert alert-warning">No guardian profile is linked to this account.</div>
    @elseif(!$inmate)
        <div class="alert alert-info">No inmate is currently linked to your guardian profile.</div>
    @else
        <div class="row g-3">
            <div class="col-12">
                <!-- Inmate Details Card -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center gap-3">
                        <img src="{{ $inmate->avatar_url }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;" alt="avatar">
                        <div>
                            <div class="h5 mb-0">{{ $inmate->full_name }}</div>
                            <div class="text-muted small">Admitted {{ optional($inmate->admission_date)->format('Y-m-d') ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="card-body small">
                        <div class="row g-3">
                            <div class="col-6 col-md-3"><div class="text-muted text-uppercase fw-semibold small">Gender</div><div>{{ $inmate->gender ?: '—' }}</div></div>
                            <div class="col-6 col-md-3"><div class="text-muted text-uppercase fw-semibold small">DOB</div><div>{{ optional($inmate->date_of_birth)->format('Y-m-d') ?: '—' }}</div></div>
                            <div class="col-6 col-md-3"><div class="text-muted text-uppercase fw-semibold small">Age</div><div>{{ optional($inmate->date_of_birth)?->age ? optional($inmate->date_of_birth)->age.' yrs' : '—' }}</div></div>
                            <div class="col-12 col-md-3"><div class="text-muted text-uppercase fw-semibold small">Notes</div><div>{{ $inmate->notes ?: '—' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <!-- Medical History Accordion Card -->
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Medical History</div>
                    <div class="card-body p-0">
                        @if($inmate->medicalRecords->isEmpty())
                            <div class="p-3 text-muted">No medical records found.</div>
                        @else
                            <div class="accordion" id="medHistoryAcc">
                                @foreach($inmate->medicalRecords->sortByDesc('created_at') as $idx => $record)
                                    @php $hid = 'rec'.$record->id; @endphp
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="h{{ $hid }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c{{ $hid }}" aria-expanded="false" aria-controls="c{{ $hid }}">
                                                <span class="me-2 bi bi-calendar-event"></span>{{ $record->created_at->format('Y-m-d H:i') }} · <span class="ms-1 badge text-bg-light">{{ $record->doctor?->name ?? 'Doctor' }}</span>
                                            </button>
                                        </h2>
                                        <div id="c{{ $hid }}" class="accordion-collapse collapse" aria-labelledby="h{{ $hid }}" data-bs-parent="#medHistoryAcc">
                                            <div class="accordion-body small">
                                                <div class="mb-2"><span class="text-muted">Diagnosis:</span> {{ $record->diagnosis ?: '—' }}</div>
                                                <div class="mb-0"><span class="text-muted">Prescription:</span> {{ $record->prescription ?: '—' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12">
                <!-- Upcoming Appointments Card -->
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Upcoming Appointments</div>
                    <div class="list-group list-group-flush">
                        @forelse(($appointments ?? []) as $appt)
                            <div class="list-group-item d-flex justify-content-between align-items-center small">
                                <div><span class="bi bi-clock me-1"></span>{{ optional($appt->scheduled_for)->format('Y-m-d H:i') }}</div>
                                <div class="fw-semibold">{{ $appt->title }}</div>
                            </div>
                        @empty
                            <div class="p-3 text-muted small">No upcoming appointments.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12">
                <!-- Shared Documents Card -->
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Shared Documents</div>
                    <div class="list-group list-group-flush">
                        @forelse(($sharedDocuments ?? collect()) as $doc)
                            <div class="list-group-item d-flex justify-content-between align-items-center small">
                                <div class="fw-semibold"><span class="bi bi-file-earmark-text me-1"></span>{{ $doc->document_name }}</div>
                                @php $disk = Storage::disk(config('filesystems.default')); $url = config('filesystems.default')==='s3' ? $disk->temporaryUrl($doc->file_path, now()->addMinutes(5)) : $disk->url($doc->file_path); @endphp
                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">Download</a>
                            </div>
                        @empty
                            <div class="p-3 text-muted small">No shared documents yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12">
                <!-- Messages Card -->
                <div class="card shadow-sm" id="guardianMessagesCard">
                    <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                        <span>Messages</span>
                    </div>
                    <div class="card-body" style="max-height: 360px; overflow:auto">
                        <div class="vstack gap-2" id="guardianThread">
                            @forelse(($messages ?? []) as $m)
                                <div class="d-flex {{ $m->sent_by_guardian ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="p-2 rounded {{ $m->sent_by_guardian ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%">
                                        <div class="small">{{ $m->message_text }}</div>
                                        <div class="text-muted small mt-1">{{ $m->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted small">No messages yet. Start the conversation below.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="card-footer">
                        <form method="POST" action="{{ route('guardian.messages.send') }}" id="guardianMsgForm" class="d-flex gap-2">
                            @csrf
                            <textarea class="form-control" name="message_text" rows="1" placeholder="Type your message..." required></textarea>
                            <button type="button" class="btn btn-primary" id="guardianMsgSend">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
    (function(){
        // Guard message form from Enter-key accidental submits
        const form = document.getElementById('guardianMsgForm');
        const btn = document.getElementById('guardianMsgSend');
        form?.closest('.card')?.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
        btn?.addEventListener('click', function(){ form?.submit(); });
        // Auto-scroll to bottom
        const cardBody = document.querySelector('#guardianMessagesCard .card-body');
        if(cardBody){ cardBody.scrollTop = cardBody.scrollHeight; }
    })();
    </script>
    @endpush
</x-app-layout>
