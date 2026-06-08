@extends('layouts.app')
@section('title','Import Facilities')
@section('topbar-title','Bulk Facility Import')

@section('content')
<div class="page-header-row">
  <div><h1>Bulk Import Facilities</h1><p>Upload a CSV to add multiple facilities at once.</p></div>
  <a href="{{ route('admin.facilities') }}" class="btn btn-secondary">← Back to Facilities</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:960px;">
  <div class="card">
    <div class="card-header"><div class="card-title">Upload CSV</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.facilities.import.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-field" style="margin-bottom:20px;">
          <label>CSV File *</label>
          <input type="file" name="csv_file" accept=".csv,.txt" required
            style="background:#fff;padding:10px;border:2px dashed var(--border);border-radius:8px;cursor:pointer;width:100%;">
          <div class="form-hint">Max 2 MB. UTF-8 encoded. First row must be column headers.</div>
          @error('csv_file')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="alert navy" style="font-size:12px;margin-bottom:16px;">
          ℹ️ Existing facilities (matched by name) will be skipped — no duplicates are created.
        </div>
        <button type="submit" class="btn btn-primary">Import Facilities</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">CSV Format</div></div>
    <div class="card-body">
      <p style="font-size:13px;color:var(--text-2);margin-bottom:14px;">Your CSV must have at least a <code style="background:#F1F5F9;padding:2px 6px;border-radius:4px;">name</code> column. All other columns are optional:</p>
      <div class="table-wrap">
        <table style="font-size:12px;">
          <thead><tr><th>Column</th><th>Required</th><th>Example</th></tr></thead>
          <tbody>
            <tr><td><code>name</code></td><td><span class="pill red" style="font-size:10px;">Required</span></td><td>Lagos Island General Hospital</td></tr>
            <tr><td><code>type</code></td><td>Optional</td><td>hospital / clinic / pharmacy</td></tr>
            <tr><td><code>state</code></td><td>Optional</td><td>Lagos</td></tr>
            <tr><td><code>city</code></td><td>Optional</td><td>Lagos Island</td></tr>
            <tr><td><code>address</code></td><td>Optional</td><td>1 Marina Road</td></tr>
            <tr><td><code>phone</code></td><td>Optional</td><td>08012345678</td></tr>
            <tr><td><code>email</code></td><td>Optional</td><td>info@hospital.ng</td></tr>
          </tbody>
        </table>
      </div>
      <div style="margin-top:16px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-2);margin-bottom:6px;">Example CSV</div>
        <pre style="background:#F8FAFC;border:1px solid var(--border);border-radius:8px;padding:12px;font-size:11px;overflow-x:auto;color:var(--text-2);">name,type,state,city
Lagos Island General,hospital,Lagos,Lagos Island
Eko Hospital,hospital,Lagos,Surulere
First Cardiology,clinic,Lagos,Victoria Island</pre>
      </div>
    </div>
  </div>
</div>
@endsection
