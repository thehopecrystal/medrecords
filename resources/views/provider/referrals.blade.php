@extends('layouts.app')
@section('title','Referral Inbox')
@section('topbar-title','Referrals')

@section('content')
<div class="page-header"><h1>Referral Inbox</h1><p>Referrals you have sent and their current status.</p></div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Patient</th><th>Referred To</th><th>Specialty</th><th>Reason</th><th>Date</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($referrals as $ref)
        <tr>
          <td>
            <div style="font-weight:600;font-size:13px;">{{ $ref->patient->user->name ?? '—' }}</div>
            <div style="font-size:11px;color:var(--text-3);">{{ $ref->patient->med_id ?? '' }}</div>
          </td>
          <td style="font-weight:600;font-size:13px;">{{ $ref->referred_to }}</td>
          <td><span class="pill navy" style="font-size:10px;">{{ $ref->specialty }}</span></td>
          <td style="font-size:12px;color:var(--text-2);max-width:200px;">{{ $ref->reason }}</td>
          <td style="font-size:12px;color:var(--text-3);">{{ $ref->created_at->format('d M Y') }}</td>
          <td>
            @php $sc=['pending'=>'amber','accepted'=>'green','completed'=>'navy','declined'=>'red'] @endphp
            <span class="pill {{ $sc[$ref->status] ?? 'grey' }}">{{ ucfirst($ref->status) }}</span>
          </td>
          <td>
            <form method="POST" action="{{ route('provider.referrals.status', $ref->id) }}" style="display:flex;gap:4px;">
              @csrf @method('PATCH')
              <select name="status" style="font-size:11px;padding:4px 6px;border:1px solid var(--border);border-radius:6px;">
                @foreach(['pending','accepted','completed','declined'] as $s)
                  <option value="{{ $s }}" {{ $ref->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
              <button type="submit" class="btn btn-ghost btn-sm">Update</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-state"><div class="empty-icon">📋</div><h3>No referrals sent</h3><p>Referrals you create for patients will appear here.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($referrals->hasPages())<div style="padding:14px 22px;border-top:1px solid var(--border);">{{ $referrals->links() }}</div>@endif
</div>
@endsection
