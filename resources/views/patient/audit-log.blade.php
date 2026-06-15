@extends('layouts.app')
@section('title', 'Access History')
@section('topbar-title', 'Who Saw My Record')

@section('content')
<div class="page-header">
  <h1>Access History</h1>
  <p>Every access to your medical record is logged here</p>
</div>

{{-- Filter bar --}}
<div class="card mb-20">
  <form method="GET" action="{{ route('patient.audit-log') }}" class="audit-filter-form">
    <div class="form-field">
      <label>Action type</label>
      <select name="action">
        <option value="">All actions</option>
        @foreach($actions as $a)
          <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-field">
      <label>Person</label>
      <input type="text" name="actor" value="{{ request('actor') }}" placeholder="Name…">
    </div>
    <div class="form-field">
      <label>From date</label>
      <input type="date" name="from" value="{{ request('from') }}">
    </div>
    <div class="form-field">
      <label>To date</label>
      <input type="date" name="to" value="{{ request('to') }}">
    </div>
    <div class="btn-group-row">
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      <a href="{{ route('patient.audit-log') }}" class="btn btn-secondary btn-sm">Clear</a>
    </div>
  </form>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Action</th>
          <th>By</th>
          <th>Description</th>
          <th>When</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td><span class="pill {{ match(true) {
              str_contains($log->action,'break_glass') => 'red',
              str_contains($log->action,'consent') => 'navy',
              str_contains($log->action,'dispense') => 'amber',
              default => 'grey'
            } }}">{{ $log->action }}</span></td>
            <td>{{ $log->actor->name }}</td>
            <td class="td-max-200">{{ $log->description }}</td>
            <td class="td-date">{{ $log->created_at->format('d M Y H:i') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4">
              <div class="empty-state empty-state-sm">
                <div class="empty-icon">🔍</div>
                <p>No access history {{ request()->hasAny(['action','actor','from','to']) ? 'matching your filters.' : 'yet.' }}</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($logs->hasPages())<div class="pager-row">{{ $logs->links() }}</div>@endif
</div>
@endsection
