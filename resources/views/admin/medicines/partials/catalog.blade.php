<div class="table-responsive">
  <table class="table table-sm align-middle mb-0">
    <thead><tr><th>Name</th><th>Form</th><th>Strength</th><th>Unit</th><th class="text-end">Add</th></tr></thead>
    <tbody>
      @forelse($medicines as $m)
      @php $existing = $inventories[$m->id] ?? null; @endphp
      <tr data-medicine-id="{{ $m->id }}">
        <td class="fw-semibold">{{ $m->name }}</td>
        <td>{{ $m->form }}</td>
        <td>{{ $m->strength }}</td>
        <td>{{ $m->unit }}</td>
        <td class="text-end">
          @if($existing)
            <span class="badge text-bg-light">Added</span>
          @else
            <div class="input-group input-group-sm" style="max-width:220px; float:right">
              <input type="number" class="form-control" placeholder="Qty" min="0" data-field="quantity">
              <input type="number" class="form-control" placeholder="Min" min="0" data-field="threshold">
              <button class="btn btn-outline-primary" data-action="add"><i class="bi bi-plus-lg"></i></button>
            </div>
          @endif
        </td>
      </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">No results.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
