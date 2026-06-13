@extends('layouts.app')
@section('title','Refill Requests')
@section('topbar-title','Refill Requests')

@section('content')
<div class="page-header-row">
  <div><h1>Refill Requests</h1><p>Review and action patient prescription refill requests.</p></div>
  @if($pendingCount > 0)<span class="pill amber">{{ $pendingCount }} pending</span>@endif
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Patient</th><th>Medication</th><th>Requested</th><th>Reason</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        @forelse($requests as $req)
        <tr>
          <td>
            <div class="person-name">{{ $req->patient->user->name ?? '—' }}</div>
            <div class="person-sub">{{ $req->patient->med_id ?? '' }}</div>
          </td>
          <td class="td-fw">{{ $req->prescription->drug_name ?? '—' }}</td>
          <td class="td-date">{{ $req->created_at->format('d M Y') }}</td>
          <td class="td-muted td-max-180">{{ $req->reason ?: '—' }}</td>
          <td>
            @php $sc = ['pending' => 'amber', 'approved' => 'green', 'denied' => 'red'] @endphp
            <span class="pill {{ $sc[$req->status] ?? 'grey' }}">{{ ucfirst($req->status) }}</span>
          </td>
          <td>
            @if($req->isPending())
            <div class="btn-group-row">
              <form method="POST" action="{{ route('provider.refills.approve', $req->id) }}">
                @csrf
                <button class="btn btn-green btn-sm">Approve</button>
              </form>
              <form method="POST" action="{{ route('provider.refills.deny', $req->id) }}"
                    data-confirm="Deny this refill request?"
                    data-confirm-ok="Deny">
                @csrf
                <input type="hidden" name="notes" value="Request denied by provider.">
                <button class="btn btn-danger btn-sm">Deny</button>
              </form>
            </div>
            @else
            <span class="td-xs-muted">{{ $req->actioned_at?->format('d M Y') ?? '—' }}</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">💊</div><h3>No refill requests</h3><p>Patients will send requests from their prescriptions page.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($requests->hasPages())<div class="pager-row">{{ $requests->links() }}</div>@endif
</div>
@endsection
