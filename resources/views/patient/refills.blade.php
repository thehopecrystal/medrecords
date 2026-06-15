@extends('layouts.app')
@section('title','Refill Requests')
@section('topbar-title','Prescription Refills')

@section('content')
<div class="page-header-row">
  <div><h1>Prescription Refills</h1><p>Request and track prescription refills from your providers.</p></div>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Medication</th><th>Provider</th><th>Requested</th><th>Reason</th><th>Status</th><th>Provider Notes</th></tr>
      </thead>
      <tbody>
        @forelse($requests as $req)
        <tr>
          <td class="td-fw">{{ $req->prescription->drug_name ?? '—' }}</td>
          <td class="td-muted">{{ $req->provider->name ?? '—' }}</td>
          <td class="td-date">{{ $req->created_at->format('d M Y') }}</td>
          <td class="td-muted td-max-180">{{ $req->reason ?: '—' }}</td>
          <td>
            @php $sc=['pending'=>'amber','approved'=>'green','denied'=>'red'] @endphp
            <span class="pill {{ $sc[$req->status] ?? 'grey' }}">{{ ucfirst($req->status) }}</span>
          </td>
          <td class="td-muted">{{ $req->provider_notes ?: '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">💊</div><h3>No refill requests</h3><p>Request refills from your health records page.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($requests->hasPages())<div class="pager-row">{{ $requests->links() }}</div>@endif
</div>

<div class="alert navy mt-16">
  💡 To request a refill, go to <a href="{{ route('patient.record','prescriptions') }}" class="text-navy-link">My Health Records → Prescriptions</a> and click "Request Refill" on an active prescription.
</div>
@endsection
