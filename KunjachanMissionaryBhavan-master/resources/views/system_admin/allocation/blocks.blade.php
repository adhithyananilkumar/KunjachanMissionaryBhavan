<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Allocation · Blocks</h2></x-slot>
  <div class="d-flex align-items-center gap-2 mb-3">
    <form method="GET" class="d-flex align-items-center gap-2">
      <select name="institution_ids[]" class="form-select form-select-sm" multiple size="1" onchange="this.form.submit()" title="Filter institutions">
        @foreach($institutions as $inst)
          <option value="{{ $inst->id }}" @selected(in_array($inst->id, ($institutionIds ?? [])))>{{ $inst->name }}</option>
        @endforeach
      </select>
      <noscript><button class="btn btn-sm btn-primary">Filter</button></noscript>
    </form>
    <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#createBlock">Create Block</button>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-light"><tr><th>Institution</th><th>Name</th><th>Prefix</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse($blocks as $b)
          <tr>
            <td>{{ $b->institution?->name }}</td>
            <td>{{ $b->name }}</td>
            <td>{{ $b->prefix ?? '—' }}</td>
            <td class="text-end">
              <div class="d-none d-md-inline-flex gap-2">
                <a href="{{ route('system_admin.blocks.locations',$b) }}" class="btn btn-sm btn-primary">Manage Locations</a>
                <button class="btn btn-sm btn-outline-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#editBlockModal"
                        data-action="{{ route('system_admin.blocks.update',$b) }}"
                        data-name="{{ $b->name }}"
                        data-prefix="{{ $b->prefix }}">Edit</button>
              </div>
              <div class="d-md-none dropstart">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="{{ route('system_admin.blocks.locations',$b) }}">Manage Locations</a></li>
                  <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editBlockModal" data-action="{{ route('system_admin.blocks.update',$b) }}" data-name="{{ $b->name }}" data-prefix="{{ $b->prefix }}">Edit</button></li>
                </ul>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center py-4">No blocks found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if($blocks->hasPages())<div class="card-footer">{{ $blocks->links() }}</div>@endif
  </div>

  <div class="modal fade" id="createBlock" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('system_admin.blocks.store') }}">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Create Block</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Institution</label>
            <select name="institution_id" class="form-select" required>
              @foreach($institutions as $inst)
                <option value="{{ $inst->id }}" @selected(($institutionId ?? null)==$inst->id)>{{ $inst->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Prefix (optional)</label><input name="prefix" class="form-control" maxlength="50"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Create</button></div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="editBlockModal" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" id="editBlockForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-header"><h5 class="modal-title">Edit Block</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Name</label><input name="name" id="editBlockName" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Prefix (optional)</label><input name="prefix" id="editBlockPrefix" class="form-control" maxlength="50"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>

  @push('scripts')
  <script>
    (function(){
      const modal = document.getElementById('editBlockModal');
      const form = document.getElementById('editBlockForm');
      const nameInput = document.getElementById('editBlockName');
      const prefixInput = document.getElementById('editBlockPrefix');
      modal.addEventListener('show.bs.modal', function(e){
        const btn = e.relatedTarget; if(!btn) return;
        form.action = btn.getAttribute('data-action');
        nameInput.value = btn.getAttribute('data-name') || '';
        prefixInput.value = btn.getAttribute('data-prefix') || '';
      });
    })();
  </script>
  @endpush
</x-app-layout>
