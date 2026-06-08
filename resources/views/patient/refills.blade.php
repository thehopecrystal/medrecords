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
          <td style="font-weight:600;">{{ $req->prescription->drug_name ?? '—' }}</td>
          <td style="font-size:12px;color:var(--text-2);">{{ $req->provider->name ?? '—' }}</td>
          <td style="font-size:12px;color:var(--text-3);">{{ $req->created_at->format('d M Y') }}</td>
          <td style="font-size:12px;color:var(--text-2);max-width:180px;">{{ $req->reason ?: '—' }}</td>
          <td>
            @php $sc=['pending'=>'amber','approved'=>'green','denied'=>'red'] @endphp
            <span class="pill {{ $sc[$req->status] ?? 'grey' }}">{{ ucfirst($req->status) }}</span>
          </td>
          <td style="font-size:12px;color:var(--text-2);">{{ $req->provider_notes ?: '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">💊</div><h3>No refill requests</h3><p>Request refills from your health records page.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($requests->hasPages())<div style="padding:14px 22px;border-top:1px solid var(--border);">{{ $requests->links() }}</div>@endif
</div>

<div class="alert navy" style="margin-top:16px;font-size:13px;">
  💡 To request a refill, go to <a href="{{ route('patient.record','prescriptions') }}" style="color:var(--navy);font-weight:700;">My Health Records → Prescriptions</a> and click "Request Refill" on an active prescription.
</div>
@endsection
