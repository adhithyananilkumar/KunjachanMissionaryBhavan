<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Counseling Progress Notes</span>
        <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addCounselingNoteForm">Add Note</button>
    </div>
    <div id="addCounselingNoteForm" class="collapse p-3">
        <form method="POST" action="{{ route('doctor.counseling-notes.store', $inmate) }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="note_date" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Progress Assessment</label>
                    <textarea name="progress_assessment" class="form-control" rows="3" required></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Milestones Achieved (optional)</label>
                    <textarea name="milestones_achieved" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12 mt-2">
                    <button class="btn btn-success btn-sm">Save Note</button>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Progress Assessment</th>
                    <th>Milestones</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
            @forelse($inmate->counselingProgressNotes()->latest('note_date')->limit(25)->get() as $note)
                <tr>
                    <td>{{ $note->note_date?->format('Y-m-d') }}</td>
                    <td style="white-space:pre-wrap">{{ $note->progress_assessment }}</td>
                    <td style="white-space:pre-wrap">{{ $note->milestones_achieved }}</td>
                    <td>{{ $note->user?->name }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted">No counseling notes yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
