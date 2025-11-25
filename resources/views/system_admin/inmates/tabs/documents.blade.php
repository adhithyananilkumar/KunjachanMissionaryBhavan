<div class="card shadow-sm">
  <div class="card-header">
    <ul class="nav nav-pills small" id="docSubTabs" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-subtab="available" type="button">Available</button></li>
      <li class="nav-item"><button class="nav-link" data-subtab="add" type="button">Add</button></li>
    </ul>
  </div>
  <div class="card-body small" id="docSubTabContent" data-upload-url="{{ route('system_admin.inmates.upload-file',$inmate) }}" data-add-url="{{ route('system_admin.inmates.documents.store',$inmate) }}">
    <style>
      /* lightweight fade for sub-panes */
      #docSubTabContent .fade-pane{display:none;opacity:0;transition:opacity .2s ease;}
      #docSubTabContent .fade-pane.show{display:block;opacity:1;}
      .doc-row.clickable{cursor:pointer;}
    </style>
    <div data-pane="available" class="fade-pane show">
      <div class="row g-3">
        <div class="col-12">
          @php $disk = Storage::disk(config('filesystems.default')); @endphp
          @php $photoUrl = $inmate->photo_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->photo_path, now()->addMinutes(5)) : $disk->url($inmate->photo_path)) : null; @endphp
          <div class="d-flex align-items-center gap-3 border rounded p-2 doc-row @if($inmate->photo_path) clickable @endif" @if($photoUrl) data-doc-url="{{ $photoUrl }}" @endif>
            <div class="text-muted small">Photo</div>
            <img src="{{ $inmate->avatar_url }}" alt="Photo" class="rounded" style="width:56px;height:56px;object-fit:cover;">
          </div>
        </div>
        @php $core = [
          'aadhaar_card' => ['Aadhaar Card',$inmate->aadhaar_card_path],
          'ration_card' => ['Ration Card',$inmate->ration_card_path],
          'panchayath_letter' => ['Panchayath Letter',$inmate->panchayath_letter_path],
          'disability_card' => ['Disability Card',$inmate->disability_card_path],
          'doctor_certificate' => ['Doctor Certificate',$inmate->doctor_certificate_path],
          'vincent_depaul_card' => ['Vincent Depaul Card',$inmate->vincent_depaul_card_path],
        ]; @endphp
        @foreach($core as $k=>[$label,$path])
          @if(!empty($path))
            @php 
              $purl = config('filesystems.default')==='s3' ? $disk->temporaryUrl($path, now()->addMinutes(5)) : $disk->url($path);
              $ext = pathinfo($path, PATHINFO_EXTENSION);
              $icon = in_array(strtolower($ext),['jpg','jpeg','png','gif','webp']) ? 'bi-image' : (strtolower($ext)==='pdf' ? 'bi-file-pdf' : 'bi-file-earmark');
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
              <div class="border rounded p-2 h-100 d-flex flex-column justify-content-between doc-row clickable" data-doc-url="{{ $purl }}">
                <div class="d-flex align-items-start gap-2">
                  <span class="bi {{ $icon }} text-primary fs-5"></span>
                  <div class="small">
                    <div class="fw-semibold">{{ $label }}</div>
                    <div class="text-muted text-truncate" style="max-width:160px;">{{ $ext ?: 'file' }}</div>
                  </div>
                </div>
                <div class="text-end"><span class="badge bg-light text-dark">Core</span></div>
              </div>
            </div>
          @endif
        @endforeach

        <div class="col-12">
          <div class="border rounded p-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="fw-semibold">Extra Documents</div>
              <div class="small text-muted">Latest first</div>
            </div>
            @php $docs = $inmate->documents()->latest()->get(); @endphp
            @if($docs->isEmpty())
              <div class="text-muted p-2">No extra documents.</div>
            @else
              <div class="row g-2">
                @foreach($docs as $d)
                  @php 
                    $durl = config('filesystems.default')==='s3' ? $disk->temporaryUrl($d->file_path, now()->addMinutes(5)) : $disk->url($d->file_path);
                    $ext = strtolower(pathinfo($d->file_path, PATHINFO_EXTENSION));
                    $isImg = in_array($ext,['jpg','jpeg','png','gif','webp']);
                    $icon = $isImg ? 'bi-image' : ($ext==='pdf' ? 'bi-filetype-pdf' : 'bi-file-earmark');
                  @endphp
                  <div class="col-12 col-md-6 col-lg-4">
                    <div class="border rounded p-2 h-100 d-flex flex-column gap-2 doc-row clickable" data-doc-url="{{ $durl }}" data-doc-name="{{ $d->document_name }}">
                      <div class="d-flex align-items-start gap-2">
                        <span class="bi {{ $icon }} text-secondary fs-5"></span>
                        <div class="small flex-grow-1">
                          <div class="fw-semibold text-truncate" title="{{ $d->document_name }}">{{ $d->document_name }}</div>
                          <div class="text-muted text-uppercase small">{{ $ext ?: 'FILE' }}</div>
                        </div>
                        <form method="POST" action="{{ route('system_admin.inmates.documents.toggle-share', [$inmate, $d]) }}" class="ms-auto d-inline">@csrf
                          <button type="submit" class="btn btn-outline-secondary btn-sm" title="Toggle share">
                            <span class="bi bi-share{{ $d->is_sharable_with_guardian ? '-fill text-success' : '' }}"></span>
                          </button>
                        </form>
                      </div>
                      @if($isImg)
                        <div class="ratio ratio-16x9 rounded overflow-hidden border bg-light">
                          <img src="{{ $durl }}" alt="{{ $d->document_name }}" style="object-fit:cover;">
                        </div>
                      @else
                        <div class="small text-muted">Open to preview</div>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

  <div data-pane="add" class="fade-pane">
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="border rounded p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-2">
              <img src="{{ $inmate->avatar_url }}" alt="Photo" class="rounded" style="width:56px;height:56px;object-fit:cover;">
              <div>
                <div class="fw-semibold">Photo</div>
                <div class="text-muted small">PNG/JPG up to 2MB</div>
              </div>
            </div>
            <form id="upload-photo-form" enctype="multipart/form-data" class="d-flex gap-2 flex-wrap">
              <input type="file" name="file" accept="image/*" class="form-control form-control-sm" required>
              <input type="hidden" name="field" value="photo">
              <button class="btn btn-primary btn-sm" type="submit">Upload</button>
            </form>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="border rounded p-3 h-100">
            <div class="fw-semibold mb-2">Add Extra Document</div>
            <form id="add-extra-doc-form" enctype="multipart/form-data" class="vstack gap-2">
              <input type="text" name="document_name" class="form-control form-control-sm" placeholder="Document name" required>
              <input type="file" name="doc_file" class="form-control form-control-sm" required>
              <button class="btn btn-primary btn-sm align-self-start" type="submit">Add Document</button>
            </form>
          </div>
        </div>

        <div class="col-12">
          <div class="border rounded p-3">
            <div class="fw-semibold mb-2">Add Core Documents</div>
            @php $coreAdd = [
              'aadhaar_card' => 'Aadhaar Card',
              'ration_card' => 'Ration Card',
              'panchayath_letter' => 'Panchayath Letter',
              'disability_card' => 'Disability Card',
              'doctor_certificate' => 'Doctor Certificate',
              'vincent_depaul_card' => 'Vincent Depaul Card',
            ]; @endphp
            <div class="row g-2">
              @foreach($coreAdd as $key=>$label)
                <div class="col-12 col-md-6">
                  <div class="border rounded p-2">
                    <div class="small text-muted mb-1">{{ $label }}</div>
                    <form class="d-flex gap-2 align-items-center upload-core-form" enctype="multipart/form-data">
                      <input type="file" name="file" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                      <input type="hidden" name="field" value="{{ $key }}">
                      <button class="btn btn-outline-primary btn-sm" type="submit">Upload</button>
                    </form>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for doc preview -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title" id="docPreviewTitle">Document</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-2" id="docPreviewBody" style="min-height:60vh; display:flex;align-items:center;justify-content:center;">
        <div class="text-muted">Loading...</div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Enhance doc preview (only once per page load)
  const container = document.getElementById('inmateTabContent');
  const observer = new MutationObserver(()=> attachHandlers());
  observer.observe(container, {childList:true, subtree:true});
  function attachHandlers(){
    const modalEl = document.getElementById('docPreviewModal');
    if(!modalEl) return;
    const rows = container.querySelectorAll('.doc-row.clickable:not([data-preview-bound])');
    rows.forEach(r=>{
      r.dataset.previewBound='1';
      r.addEventListener('click', (e)=>{
        const url = r.getAttribute('data-doc-url');
        if(!url) return;
        const name = r.getAttribute('data-doc-name') || 'Document';
        const body = document.getElementById('docPreviewBody');
        const title = document.getElementById('docPreviewTitle');
        if(body){ body.innerHTML = '<div class="text-muted">Loading preview...</div>'; }
        if(title){ title.textContent = name; }
        const ext = (url.split('?')[0].split('.').pop()||'').toLowerCase();
        let content='';
        if(['jpg','jpeg','png','gif','webp'].includes(ext)){
          content = `<img src="${url}" alt="${name}" style="max-width:100%;max-height:75vh;object-fit:contain;" />`;
        } else if(ext==='pdf') {
          content = `<iframe src="${url}" style="width:100%;height:75vh;border:0;" title="${name}"></iframe>`;
        } else {
          content = `<div class='text-center p-3 small'><a href='${url}' target='_blank' rel='noopener'>Open File</a></div>`;
        }
        if(body){ body.innerHTML = content; }
        if(typeof bootstrap !== 'undefined'){
          const m = bootstrap.Modal.getOrCreateInstance(modalEl); m.show();
        } else {
          window.open(url,'_blank');
        }
      });
    });
  }
  attachHandlers();
});
</script>
@endpush
