<div class="card shadow-sm">
  <div class="card-header">
    <ul class="nav nav-pills small" id="docSubTabs" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-subtab="available" type="button">Available</button></li>
      <li class="nav-item"><button class="nav-link" data-subtab="add" type="button">Add</button></li>
    </ul>
  </div>
  <div class="card-body small" id="docSubTabContent" data-upload-url="{{ route('admin.inmates.upload-file',$inmate) }}" data-add-url="{{ route('admin.inmates.documents.store',$inmate) }}">
    <style>
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
            <div class="col-12">
              @php $purl = config('filesystems.default')==='s3' ? $disk->temporaryUrl($path, now()->addMinutes(5)) : $disk->url($path); @endphp
              <div class="d-flex align-items-center justify-content-between border rounded p-2 doc-row clickable" data-doc-url="{{ $purl }}">
                <div class="fw-semibold">{{ $label }}</div>
                <span class="bi bi-box-arrow-up-right text-muted"></span>
              </div>
            </div>
          @endif
        @endforeach

        <div class="col-12">
          <div class="border rounded">
            <div class="p-2 fw-semibold">Extra Documents</div>
            @php $docs = $inmate->documents()->latest()->get(); @endphp
            @if($docs->isEmpty())
              <div class="text-muted p-2">No extra documents.</div>
            @else
              <div class="list-group list-group-flush">
                @foreach($docs as $d)
                  <div class="list-group-item d-flex justify-content-between align-items-center">
                    @php $durl = config('filesystems.default')==='s3' ? $disk->temporaryUrl($d->file_path, now()->addMinutes(5)) : $disk->url($d->file_path); @endphp
                    <a class="text-decoration-none d-flex align-items-center gap-2" href="{{ $durl }}" target="_blank">
                      <span class="bi bi-file-earmark-text text-muted"></span>{{ $d->document_name }}
                    </a>
                    <form method="POST" action="{{ route('admin.inmates.documents.toggle-share', [$inmate, $d]) }}" class="d-inline">@csrf
                      <button type="submit" class="btn btn-sm {{ $d->is_sharable_with_guardian ? 'btn-success' : 'btn-outline-secondary' }}">
                        <span class="bi bi-share me-1"></span>{{ $d->is_sharable_with_guardian ? 'Shared with Guardian' : 'Share with Guardian' }}
                      </button>
                    </form>
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
