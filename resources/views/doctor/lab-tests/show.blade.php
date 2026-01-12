<x-app-layout>
  <x-slot name="header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h2 class="h5 mb-0">Lab Test Details</h2>
      <div class="d-flex gap-2">
        @if(!$labTest->reviewed_at && $labTest->status==='completed')
          <form method="POST" action="{{ route('doctor.lab-tests.update',$labTest) }}" onsubmit="return confirm('Accept and lock this report?');" class="d-inline">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="{{ $labTest->status }}">
            <button class="btn btn-success btn-sm"><span class="bi bi-check2-circle me-1"></span>Accept</button>
          </form>
          <button class="btn btn-outline-danger btn-sm" data-bs-toggle="collapse" data-bs-target="#rejectBox" aria-expanded="false" aria-controls="rejectBox"><span class="bi bi-x-circle me-1"></span>Reject</button>
        @endif
      </div>
    </div>
  </x-slot>
  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Patient</dt>
        <dd class="col-sm-9"><a href="{{ route('doctor.inmates.show',$labTest->inmate_id) }}">{{ $labTest->inmate?->full_name ?? ('Inmate #'.$labTest->inmate_id) }}</a></dd>
        <dt class="col-sm-3">Test</dt><dd class="col-sm-9">{{ $labTest->test_name }}</dd>
        <dt class="col-sm-3">Ordered</dt><dd class="col-sm-9">{{ optional($labTest->ordered_date)->format('Y-m-d H:i') }} by {{ $labTest->orderedBy?->name }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9 text-capitalize">{{ str_replace('_',' ',$labTest->status) }}</dd>
        <dt class="col-sm-3">Completed</dt><dd class="col-sm-9">{{ optional($labTest->completed_date)->format('Y-m-d H:i') ?: '—' }}</dd>
        <dt class="col-sm-3">Reviewed</dt><dd class="col-sm-9">@if($labTest->reviewed_at) {{ $labTest->reviewed_at->format('Y-m-d H:i') }} by {{ $labTest->reviewedBy?->name }} @else — @endif</dd>
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $labTest->notes ?: '—' }}</dd>
  <dt class="col-sm-3">Result Notes</dt><dd id="labResultNotes" class="col-sm-9">{{ $labTest->result_notes ?: '—' }}</dd>
        <dt class="col-sm-3">Report</dt>
        <dd class="col-sm-9">
          @if($labTest->result_file_path)
            @php $disk = Storage::disk(config('filesystems.default')); $url = $labTest->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($labTest->result_file_path, now()->addMinutes(5)) : $disk->url($labTest->result_file_path)) : null; @endphp
            <a target="_blank" href="{{ $url }}" class="btn btn-light border d-inline-flex align-items-center gap-2 shadow-sm">
              <img src="{{ asset('assets/aathmiya.png') }}" alt="logo" style="height:18px;width:auto"/>
              <span class="bi bi-download"></span>
              <span>Download report</span>
            </a>
          @else
            <span class="text-muted">No report file uploaded</span>
          @endif
        </dd>
      </dl>
      <div class="mt-3 d-flex flex-wrap gap-2">
        <a href="{{ route('doctor.lab-tests.index') }}" class="btn btn-secondary">Back</a>
        <button class="btn btn-primary d-none d-sm-inline-flex" type="button" data-bs-toggle="offcanvas" data-bs-target="#addRecordCanvas" aria-controls="addRecordCanvas"><span class="bi bi-clipboard2-plus me-1"></span>Add Medical Record</button>
        @if(!$labTest->reviewed_at && $labTest->status==='completed')
          <form method="POST" action="{{ route('doctor.lab-tests.update',$labTest) }}" class="d-inline w-100 w-md-auto">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="{{ $labTest->status }}">
            <input type="hidden" name="accept" value="1">
            <div class="mb-2">
              <label class="form-label small">Doctor Review (optional)</label>
              <textarea class="form-control" name="doctor_review" rows="2" placeholder="Summarize the result assessment / plan"></textarea>
            </div>
            <button class="btn btn-success"><span class="bi bi-check2-circle me-1"></span>Accept & Save</button>
          </form>
          <div class="collapse w-100" id="rejectBox">
            <div class="card card-body border-danger-subtle">
              <form method="POST" action="{{ route('doctor.lab-tests.update',$labTest) }}" onsubmit="return confirm('Reject this report and send back for updates?');">
                @csrf @method('PUT')
                <input type="hidden" name="reject" value="1">
                <div class="mb-2">
                  <label class="form-label small">Reason (optional)</label>
                  <textarea class="form-control" name="doctor_review" rows="2" placeholder="What needs to be corrected or added?"></textarea>
                </div>
                <button class="btn btn-outline-danger"><span class="bi bi-x-circle me-1"></span>Send Back</button>
              </form>
            </div>
          </div>
        @endif
      </div>
      <hr class="my-4">
      <!-- Mobile FAB to open the bottom sheet -->
      <button type="button" class="btn btn-primary position-fixed d-sm-none" style="right:1rem; bottom:1rem; z-index:1031; border-radius:999px; padding:.75rem 1rem; box-shadow:0 0.25rem 1rem rgba(0,0,0,.15);" data-bs-toggle="offcanvas" data-bs-target="#addRecordCanvas" aria-controls="addRecordCanvas">
        <span class="bi bi-clipboard2-plus"></span>
      </button>

      <!-- Bottom offcanvas: Add Medical Record -->
      <div class="offcanvas offcanvas-bottom" tabindex="-1" id="addRecordCanvas" aria-labelledby="addRecordCanvasLabel" style="height: auto; max-height: 80vh;">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="addRecordCanvasLabel"><span class="bi bi-clipboard2-plus me-1"></span> Add Medical Record <small class="text-muted">(linked to this lab)</small></h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body small">
          <form method="POST" action="{{ route('doctor.inmates.medical-records.store', $labTest->inmate_id) }}">
            @csrf
            <input type="hidden" name="lab_test_id" value="{{ $labTest->id }}">
            <div class="row g-2 align-items-start">
              <div class="col-12 col-lg-6">
                <label class="form-label">Diagnosis</label>
                <textarea name="diagnosis" class="form-control" rows="2" placeholder="E.g., Review of {{ $labTest->test_name }} — enter diagnosis/assessment" required>{{ old('diagnosis') }}</textarea>
                @error('diagnosis')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                @if($labTest->result_notes)
                  <div class="mt-1">
                    <button type="button" class="btn btn-link btn-sm p-0" id="useResultNotesBtn">Use result notes in diagnosis</button>
                  </div>
                @endif
              </div>
              <div class="col-12 col-lg-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label mb-0">Medicines</label>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="addMedRowBtn"><span class="bi bi-plus-lg me-1"></span>Add medicine</button>
                </div>
                <div id="medsContainer" class="d-flex flex-column gap-2"></div>
                <small class="text-muted">Optional. Add one or more medicines.</small>
              </div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-8">
                <label class="form-label">Prescription (optional)</label>
                <textarea name="prescription" class="form-control" rows="1" placeholder="Optional notes / prescription">{{ old('prescription') }}</textarea>
              </div>
              <div class="col-12 col-md-4 d-flex gap-2 align-items-end justify-content-md-end">
                <a href="{{ route('doctor.inmates.show', $labTest->inmate_id) }}" class="btn btn-outline-secondary flex-grow-1 flex-md-grow-0">Patient Profile</a>
                <button class="btn btn-primary flex-grow-1 flex-md-grow-0"><span class="bi bi-save me-1"></span> Save</button>
              </div>
            </div>
          </form>
          <template id="medRowTemplate">
            <div class="border rounded p-2 bg-light-subtle">
              <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                  <label class="form-label small">Name</label>
                  <input name="med_name[]" class="form-control form-control-sm" placeholder="e.g., Amoxicillin">
                </div>
                <div class="col-6 col-md-2">
                  <label class="form-label small">Dosage</label>
                  <input name="med_dosage[]" class="form-control form-control-sm" placeholder="500 mg">
                </div>
                <div class="col-6 col-md-2">
                  <label class="form-label small">Route</label>
                  <input name="med_route[]" class="form-control form-control-sm" placeholder="PO">
                </div>
                <div class="col-12 col-md-4">
                  <label class="form-label small">Frequency</label>
                  <input name="med_frequency[]" class="form-control form-control-sm" placeholder="BID">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">Start date</label>
                  <input type="date" name="med_start_date[]" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label small">End date</label>
                  <input type="date" name="med_end_date[]" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-md-5">
                  <label class="form-label small">Instructions</label>
                  <input name="med_instructions[]" class="form-control form-control-sm" placeholder="After meals">
                </div>
                <div class="col-12 col-md-1 text-end">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-med-row" title="Remove"><span class="bi bi-trash"></span></button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', ()=>{
      // If opened from notifications, mark it read
      try{
        const usp = new URLSearchParams(location.search);
        const nid = usp.get('nid');
        if(nid){ fetch(`/notifications/${nid}/mark-read`, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}); }
      }catch(e){}

      const useBtn = document.getElementById('useResultNotesBtn');
      if(useBtn){
        useBtn.addEventListener('click', ()=>{
          const box = document.getElementById('labResultNotes');
          const ta = document.querySelector('textarea[name="diagnosis"]');
          if(ta && box){
            const txt = box.textContent.trim();
            if(txt && txt !== '—'){
              ta.value = (ta.value? ta.value+'\n\n' : '') + txt;
              ta.focus();
            }
          }
        });
      }

      const addBtn = document.getElementById('addMedRowBtn');
      const container = document.getElementById('medsContainer');
      const tpl = document.getElementById('medRowTemplate');
      function addMedRow(){ if(tpl && container){ container.appendChild(tpl.content.cloneNode(true)); } }
      if(addBtn){ addBtn.addEventListener('click', addMedRow); }
      document.addEventListener('click', (e)=>{
        const rm = e.target.closest?.('.remove-med-row');
        if(rm){
          const card = rm.closest('.border');
          if(card && card.parentElement){ card.parentElement.removeChild(card); }
        }
      });
      // Auto-add one medicine row when offcanvas opens
      const offcanvasEl = document.getElementById('addRecordCanvas');
      if(offcanvasEl){
        offcanvasEl.addEventListener('shown.bs.offcanvas', ()=>{
          if(container && container.children.length === 0){ addMedRow(); }
        });
      }
    });
  </script>
  @endpush
</x-app-layout>