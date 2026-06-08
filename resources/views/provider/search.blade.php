@extends('layouts.app')
@section('title', 'Patient Lookup')
@section('topbar-title', 'Patient Lookup')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Provider</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('provider.dashboard') }}" class="nav-item">🏥 Dashboard</a>
    <a href="{{ route('provider.search') }}" class="nav-item active">🔍 Patient lookup</a>
  </nav>
  <div class="sidebar-footer"><div class="name">{{ auth()->user()->name }}</div>Provider</div>
</aside>
@endsection

@section('content')
<div class="page-header">
  <h1>Patient Lookup</h1>
  <p>Search by MedID, NIN, phone, or name</p>
</div>

<div class="card" style="margin-bottom:20px;">
  <form method="GET" action="{{ route('provider.search') }}" style="display:flex;gap:12px;">
    <input type="text" name="q" value="{{ $q }}" placeholder="Search by MedID, name, phone, or email…"
      style="flex:1;background:var(--grey-50);border:1px solid var(--grey-200);border-radius:8px;padding:10px 14px;font-size:14px;" autofocus>
    <button type="submit" class="btn btn-primary">Search</button>
  </form>
</div>

@if(strlen($q) >= 2)
  @if($patients->count())
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Name</th><th>MedID</th><th>DOB / Age</th><th>Phone</th><th>Action</th></tr>
          </thead>
          <tbody>
            @foreach($patients as $p)
              <tr>
                <td><strong>{{ $p->user->name }}</strong></td>
                <td><span style="font-family:monospace;font-size:12px;">{{ $p->med_id }}</span></td>
                <td>{{ $p->dob ? $p->dob->format('d M Y') . ' (' . $p->age . ' yrs)' : '—' }}</td>
                <td>{{ $p->user->phone }}</td>
                <td>
                  <a href="{{ route('provider.patient', $p->id) }}" class="btn btn-primary btn-sm">View record</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="alert navy"><span>ℹ</span><span>No patients found for "{{ $q }}".</span></div>
  @endif
@endif
@endsection
