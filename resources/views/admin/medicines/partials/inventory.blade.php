@foreach($inventories as $inv)
<div class="list-group-item d-flex align-items-center justify-content-between" data-id="{{ $inv->id }}">
  <div>
    <div class="fw-semibold">{{ $inv->medicine->name }}</div>
    <div class="text-muted small">{{ $inv->medicine->form }} {{ $inv->medicine->strength }} {{ $inv->medicine->unit }}</div>
  </div>
  <div class="text-end" style="min-width:160px">
    <div class="input-group input-group-sm">
      <span class="input-group-text">Qty</span>
      <input type="number" class="form-control" value="{{ $inv->quantity }}" data-field="quantity" min="0">
      <span class="input-group-text">Min</span>
      <input type="number" class="form-control" value="{{ $inv->threshold }}" data-field="threshold" min="0">
      <button class="btn btn-outline-primary" data-action="save"><i class="bi bi-check2"></i></button>
    </div>
    <div class="small mt-1">
      @if($inv->quantity <= $inv->threshold)
        <span class="badge text-bg-danger-subtle text-danger">Low</span>
      @else
        <span class="badge text-bg-light">OK</span>
      @endif
      <button class="btn btn-link btn-sm text-danger p-0 ms-2" data-action="remove">Remove</button>
    </div>
  </div>
</div>
@endforeach
@if($inventories->isEmpty())
<div class="p-3 text-muted small">No inventory yet. Add from catalog →</div>
@endif
