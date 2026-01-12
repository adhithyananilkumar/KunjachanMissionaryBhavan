@extends('layouts.app')
@section('title','Medication Windows — System Admin')
@section('content')
<div class="container py-3">
  <h1 class="h5 mb-3">Medication Time Windows</h1>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  <form method="post" action="{{ route('system_admin.settings.medication-windows.save') }}" class="card p-3">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header fw-semibold">Morning</div>
          <div class="card-body">
            <label class="form-label small">Start</label>
            <input type="time" name="morning_start" class="form-control" value="{{ $windows['morning'][0] ?? '07:30' }}">
            <label class="form-label small mt-2">End</label>
            <input type="time" name="morning_end" class="form-control" value="{{ $windows['morning'][1] ?? '10:30' }}">
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header fw-semibold">Noon</div>
          <div class="card-body">
            <label class="form-label small">Start</label>
            <input type="time" name="noon_start" class="form-control" value="{{ $windows['noon'][0] ?? '12:00' }}">
            <label class="form-label small mt-2">End</label>
            <input type="time" name="noon_end" class="form-control" value="{{ $windows['noon'][1] ?? '14:00' }}">
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header fw-semibold">Night</div>
          <div class="card-body">
            <label class="form-label small">Start</label>
            <input type="time" name="night_start" class="form-control" value="{{ $windows['night'][0] ?? '19:00' }}">
            <label class="form-label small mt-2">End</label>
            <input type="time" name="night_end" class="form-control" value="{{ $windows['night'][1] ?? '22:00' }}">
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-end"><button class="btn btn-primary">Save</button></div>
    <p class="text-muted small mt-2">These values are stored in .env as MED_WINDOW_* keys and take effect immediately.</p>
  </form>
</div>
@endsection
