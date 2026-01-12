<div class="card mt-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Educational Records</span>
    @if(isset($inmate))
    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#educationalRecordModal">Add</button>
    @endif
  </div>
  <div class="card-body">
    @if(isset($inmate) && $inmate->educationalRecords->count())
      <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0 align-middle">
          <thead class="table-light"><tr><th>School</th><th>Grade</th><th>Year</th><th>Notes</th><th>Transcript</th><th>Created</th></tr></thead>
          <tbody>
            @foreach($inmate->educationalRecords as $rec)
              <tr>
                <td>{{ $rec->school_name }}</td>
                <td>{{ $rec->grade }}</td>
                <td>{{ $rec->academic_year }}</td>
                <td>{{ Str::limit($rec->notes,60) }}</td>
                <td>
                  @if($rec->subjects_and_grades)
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#transcriptModal" data-transcript='@json($rec->subjects_and_grades)' data-record="{{ $rec->id }}">View Transcript</button>
                  @else
                  <span class="text-muted small">—</span>
                  @endif
                </td>
                <td class="text-nowrap small">{{ $rec->created_at->format('Y-m-d') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <p class="text-muted mb-0">No educational records yet.</p>
    @endif
  </div>
</div>

@if(isset($inmate))
<!-- Add/Edit Educational Record Modal -->
<div class="modal fade" id="educationalRecordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Educational Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="{{ route('admin.educational-records.store',$inmate) }}" id="eduRecordForm">@csrf
      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-4"><label class="form-label small">School Name</label><input name="school_name" class="form-control form-control-sm" required></div>
          <div class="col-md-2"><label class="form-label small">Overall Grade</label><input name="grade" class="form-control form-control-sm"></div>
          <div class="col-md-2"><label class="form-label small">Academic Year</label><input name="academic_year" class="form-control form-control-sm"></div>
          <div class="col-md-4"><label class="form-label small">Notes</label><input name="notes" class="form-control form-control-sm"></div>
        </div>
        <hr>
        <h6 class="mb-2">Subjects & Grades</h6>
        <div id="subjectsWrapper" class="mb-2"></div>
        <button type="button" id="addSubjectRow" class="btn btn-sm btn-outline-primary">Add Subject</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success">Save Record</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Transcript Modal -->
<div class="modal fade" id="transcriptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Transcript</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div id="transcriptContent"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button></div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const wrapper = document.getElementById('subjectsWrapper');
  const addBtn = document.getElementById('addSubjectRow');
  function addRow(sub='', grade=''){
    const div = document.createElement('div');
    div.className='row g-2 align-items-end mb-2 subject-row';
    div.innerHTML=`<div class="col-7"><input name="subjects[subject][]" class="form-control form-control-sm" placeholder="Subject" value="${sub.replace(/"/g,'&quot;')}"></div>
      <div class="col-3"><input name="subjects[grade][]" class="form-control form-control-sm" placeholder="Grade" value="${grade.replace(/"/g,'&quot;')}"></div>
      <div class="col-2 d-grid"><button type="button" class="btn btn-outline-danger btn-sm remove-row">X</button></div>`;
    wrapper.appendChild(div);
  }
  if(addBtn){ addBtn.addEventListener('click', ()=> addRow()); }
  wrapper.addEventListener('click', e=>{ if(e.target.classList.contains('remove-row')) e.target.closest('.subject-row').remove(); });
  // Always start with one row
  addRow();

  // Transcript modal population
  const transcriptModal = document.getElementById('transcriptModal');
  transcriptModal.addEventListener('show.bs.modal', function(ev){
    const button = ev.relatedTarget; if(!button) return;
    const data = button.getAttribute('data-transcript');
    let list = [];
    try { list = JSON.parse(data); } catch(e) {}
    const container = document.getElementById('transcriptContent');
    if(!list || !list.length){ container.innerHTML='<p class="text-muted mb-0">No transcript data.</p>'; return; }
    let html = '<table class="table table-sm"><thead><tr><th>Subject</th><th>Grade</th></tr></thead><tbody>';
    list.forEach(r=>{ html+=`<tr><td>${r.subject}</td><td>${r.grade}</td></tr>`; });
    html+='</tbody></table>';
    container.innerHTML = html;
  });
});
</script>
@endpush
@endif
