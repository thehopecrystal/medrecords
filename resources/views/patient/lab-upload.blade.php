@extends('layouts.app')
@section('title','Upload Lab Result')
@section('topbar-title','Upload Lab Result')

@section('content')
<div class="page-header-row">
  <div><h1>Upload Lab Result</h1><p>Add a lab result from an external lab or clinic.</p></div>
  <a href="{{ route('patient.record','labs') }}" class="btn btn-secondary">← Back to Records</a>
</div>

<div class="max-640">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Lab Result Details</div>
      <div class="card-sub">Patient-uploaded results are marked separately from provider-ordered tests.</div>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('patient.lab-upload.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid mb-16">
          <div class="form-field span-2">
            <label>Test Name *</label>
            <input type="text" name="test_name" value="{{ old('test_name') }}" required placeholder="e.g. Full Blood Count, HbA1c, Lipid Panel">
            @error('test_name')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field span-2">
            <label>Result *</label>
            <input type="text" name="result" value="{{ old('result') }}" required placeholder="e.g. 5.4 mmol/L, Negative, See file">
            @error('result')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Collection Date *</label>
            <input type="date" name="collected_at" value="{{ old('collected_at') }}" max="{{ today()->toDateString() }}" required>
            @error('collected_at')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Flag</label>
            <select name="flag">
              <option value="normal"   {{ old('flag','normal')=='normal'   ?'selected':'' }}>Normal</option>
              <option value="high"     {{ old('flag')=='high'     ?'selected':'' }}>High</option>
              <option value="low"      {{ old('flag')=='low'      ?'selected':'' }}>Low</option>
              <option value="critical" {{ old('flag')=='critical' ?'selected':'' }}>Critical</option>
            </select>
          </div>
          <div class="form-field span-2">
            <label>Notes</label>
            <textarea name="notes" rows="3" placeholder="Lab name, doctor's comments, reference range…">{{ old('notes') }}</textarea>
          </div>
          <div class="form-field span-2">
            <label>Attach File <span class="td-muted" style="font-weight:400;">(PDF, JPG, PNG — max 5 MB)</span></label>
            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="file-input-zone">
            @error('file')<div class="form-error">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="alert amber mb-16">
          ⚠️ Only upload genuine lab results. Patient-uploaded results are flagged as self-reported and do not replace clinician-ordered tests.
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Upload Result</button>
          <a href="{{ route('patient.record','labs') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
