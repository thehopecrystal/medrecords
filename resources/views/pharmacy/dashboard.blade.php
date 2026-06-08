@extends('layouts.app')
@section('title', 'Pharmacy Dashboard')
@section('topbar-title', 'Pharmacy Workstation')


@section('content')
<div class="page-header-row">
  <div>
    <h1>Pharmacy Workstation</h1>
    <p style="color:var(--grey-500);font-size:13px;">{{ now()->format('l, d F Y') }}</p>
  </div>
  <a href="{{ route('pharmacy.lookup') }}" class="btn btn-primary">Verify prescription</a>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-label">Dispensed today</div><div class="stat-value">{{ $recentDispenses->where('created_at', '>=', today())->count() }}</div></div>
  <div class="stat-card green"><div class="stat-label">This week</div><div class="stat-value">{{ $recentDispenses->where('created_at', '>=', now()->startOfWeek())->count() }}</div></div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Recent dispenses</div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Patient</th><th>Drug</th><th>Prescriber</th><th>Qty</th><th>Price</th><th>When</th></tr>
      </thead>
      <tbody>
        @forelse($recentDispenses as $d)
          <tr>
            <td><strong>{{ $d->prescription->patient->user->name }}</strong></td>
            <td>{{ $d->prescription->drug_name }} {{ $d->prescription->dose }}</td>
            <td>{{ $d->prescription->prescribedBy->name }}</td>
            <td>{{ $d->quantity }}</td>
            <td>{{ $d->price ? 'NGN ' . number_format($d->price) : '—' }}</td>
            <td style="font-size:12px;color:var(--grey-500);">{{ $d->created_at->diffForHumans() }}</td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--grey-500);padding:24px;">No dispenses yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
