<div class="card shadow-sm">
  <div class="card-header">
    <ul class="nav nav-pills small" id="docSubTabs" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-subtab="available" type="button">Documents</button></li>
      <li class="nav-item"><button class="nav-link" data-subtab="add" type="button">Add</button></li>
      <li class="nav-item"><button class="nav-link" data-subtab="archives" type="button">Archives</button></li>
    </ul>
  </div>

  <div class="card-body small" id="docSubTabContent" data-upload-url="{{ route('admin.inmates.upload-file',$inmate) }}" data-add-url="{{ route('admin.inmates.documents.store',$inmate) }}">
    <style>
      #docSubTabContent .fade-pane{display:none;opacity:0;transition:opacity .2s ease;}
      #docSubTabContent .fade-pane.show{display:block;opacity:1;}
      .doc-row.clickable{cursor:pointer;}
      .doc-file-input{display:none;}
    </style>

    @php $disk = Storage::disk(config('filesystems.default')); @endphp

    <div data-pane="available" class="fade-pane show">
      <div class="row g-3">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">Core Documents</div>
            <div class="text-muted small">Click to preview</div>
          </div>
          @php
            $photoUrl = $inmate->photo_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->photo_path, now()->addMinutes(5)) : $disk->url($inmate->photo_path)) : null;
            $core = [
              'photo' => ['Photo',$inmate->photo_path,'image/*'],
              'aadhaar_card' => ['Aadhaar Card',$inmate->aadhaar_card_path,'.pdf,.jpg,.jpeg,.png'],
              'ration_card' => ['Ration Card',$inmate->ration_card_path,'.pdf,.jpg,.jpeg,.png'],
              'panchayath_letter' => ['Panchayath Letter',$inmate->panchayath_letter_path,'.pdf,.jpg,.jpeg,.png'],
              'disability_card' => ['Disability Card',$inmate->disability_card_path,'.pdf,.jpg,.jpeg,.png'],
              'doctor_certificate' => ['Doctor Certificate',$inmate->doctor_certificate_path,'.pdf,.jpg,.jpeg,.png'],
              'vincent_depaul_card' => ['Vincent Depaul Card',$inmate->vincent_depaul_card_path,'.pdf,.jpg,.jpeg,.png'],
            ];
          @endphp
          <div class="row g-2">
            @foreach($core as $key => [$label,$path,$accept])
              @php
                $exists = !empty($path);
                $url = null;
                if($exists){
                  $url = config('filesystems.default')==='s3'
                    ? $disk->temporaryUrl($path, now()->addMinutes(5))
                    : $disk->url($path);
                }
                $ext = $exists ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';
                $isImg = in_array($ext,['jpg','jpeg','png','gif','webp']) || $key==='photo';
                $icon = $isImg ? 'bi-image' : ($ext==='pdf' ? 'bi-filetype-pdf' : 'bi-file-earmark');
              @endphp
              <div class="col-12 col-md-6 col-lg-4">
                <div class="border rounded p-2 h-100 d-flex flex-column gap-2 doc-row {{ $exists ? 'clickable' : '' }}" @if($url) data-doc-url="{{ $url }}" data-doc-name="{{ $label }}" @endif>
                  <div class="d-flex align-items-start gap-2">
                    <span class="bi {{ $icon }} text-primary fs-5"></span>
                    <div class="small flex-grow-1">
                      <div class="fw-semibold">{{ $label }}</div>
                      <div class="text-muted small">{{ $exists ? strtoupper($ext ?: 'FILE') : 'Not uploaded' }}</div>
                    </div>
                    <span class="badge {{ $exists ? 'bg-success' : 'bg-light text-dark' }}">{{ $exists ? 'Available' : 'Missing' }}</span>
                  </div>

                  <div class="d-flex gap-2 align-items-center flex-wrap">
                    <button type="button" class="btn btn-outline-secondary btn-sm doc-open-btn" data-doc-url="{{ $url }}" data-doc-name="{{ $label }}" {{ $exists ? '' : 'disabled' }}>
                      <span class="bi bi-eye me-1"></span>View
                    </button>
                    <form class="upload-core-form d-inline-flex align-items-center gap-2 m-0" enctype="multipart/form-data">
                      <input type="hidden" name="field" value="{{ $key }}">
                      <input type="file" name="file" class="doc-file-input" accept="{{ $accept }}" required>
                      <button type="button" class="btn btn-primary btn-sm doc-pick-btn">
                        <span class="bi bi-arrow-repeat me-1"></span>{{ $exists ? 'Replace' : 'Upload' }}
                      </button>
                      <button type="submit" class="btn btn-primary btn-sm d-none">Upload</button>
                    </form>
                  </div>

                  @if($key==='photo')
                    <div class="d-flex align-items-center gap-2">
                      <img src="{{ $inmate->avatar_url }}" alt="Photo" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                      <div class="text-muted small">Current avatar preview</div>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>

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
                        <form method="POST" action="{{ route('admin.inmates.documents.toggle-share', [$inmate, $d]) }}" class="ms-auto d-inline">@csrf
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
                        <div class="small text-muted">Click to preview</div>
                      @endif

                      <div class="d-flex gap-2 align-items-center flex-wrap">
                        <form class="replace-extra-form d-inline-flex align-items-center gap-2 m-0" action="{{ route('admin.inmates.documents.replace', [$inmate, $d]) }}" method="POST" enctype="multipart/form-data">@csrf
                          <input type="file" name="doc_file" class="doc-file-input" required>
                          <button type="button" class="btn btn-primary btn-sm doc-pick-btn">
                            <span class="bi bi-arrow-repeat me-1"></span>Replace
                          </button>
                          <button type="submit" class="btn btn-primary btn-sm d-none">Replace</button>
                        </form>
                      </div>
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
            <div class="fw-semibold mb-2">Add Extra Document</div>
            <form id="add-extra-doc-form" enctype="multipart/form-data" class="vstack gap-2">
              <input type="text" name="document_name" class="form-control form-control-sm" placeholder="Document name" required>
              <input type="file" name="doc_file" class="form-control form-control-sm" required>
              <button class="btn btn-primary btn-sm align-self-start" type="submit">Add Document</button>
            </form>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="border rounded p-3 h-100">
            <div class="fw-semibold mb-2">Quick Tip</div>
            <div class="text-muted small">Use Replace from the Documents tab when a file already exists. Old versions are kept in Archives.</div>
          </div>
        </div>
      </div>
    </div>

    <div data-pane="archives" class="fade-pane">
      @php $archives = $inmate->documentArchives()->latest('archived_at')->get(); @endphp
      <div class="border rounded p-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold">Archived Versions</div>
          <div class="small text-muted">Old files kept automatically</div>
        </div>
        @if($archives->isEmpty())
          <div class="text-muted p-2">No archived documents yet.</div>
        @else
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Name</th>
                  <th>Archived</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($archives as $a)
                  @php
                    $aurl = config('filesystems.default')==='s3'
                      ? $disk->temporaryUrl($a->file_path, now()->addMinutes(5))
                      : $disk->url($a->file_path);
                    $label = $a->document_name ?: ($a->source_key ?: 'Document');
                  @endphp
                  <tr>
                    <td><span class="badge bg-light text-dark text-uppercase">{{ $a->source_type }}</span></td>
                    <td class="text-truncate" style="max-width:280px;">{{ $label }}</td>
                    <td class="text-muted">{{ optional($a->archived_at)->format('d M Y, h:i A') }}</td>
                    <td class="text-end">
                      <button type="button" class="btn btn-outline-secondary btn-sm doc-open-btn" data-doc-url="{{ $aurl }}" data-doc-name="{{ $label }}">
                        <span class="bi bi-eye me-1"></span>View
                      </button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal for doc preview (populated by show.blade tab JS) -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title" id="docPreviewTitle">Document</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-2" id="docPreviewBody" style="min-height:60vh; display:flex;align-items:center;justify-content:center;">
        <div class="text-muted">Select a document to preview.</div>
      </div>
    </div>
  </div>
</div>
