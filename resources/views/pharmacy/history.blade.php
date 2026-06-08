@extends('layouts.app')
@section('title', 'Dispense History')
@section('topbar-title', 'Dispense History')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Pharmacy</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('pharmacy.dashboard') }}" class="nav-item">🏥 Dashboard</a>
    <a href="{{ route('pharmacy.lookup') }}" class="nav-item">🔍 Verify prescription</a>
    <a href="{{ route('pharmacy.history') }}" class="nav-item active">📋 Dispense history</a>
  </nav>
  <div class="sidebar-footer"><div class="name">{{ auth()->user()->name }}</div>Pharmacist</div>
</aside>
@endsection

@section('content')
<div class="page-header"><h1>Dispense History</h1><p>All prescriptions you have dispensed</p></div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Patient</th><th>Drug</th><th>Qty</th><th>Batch</th><th>Price</th><th>Payment</th><th>Date</th></tr>
      </thead>
      <tbody>
        @forelse($dispenses as $d)
          <tr>
            <td><strong>{{ $d->prescription->patient->user->name }}</strong><br><span style="font-size:11px;color:var(--grey-500);">{{ $d->prescription->patient->med_id }}</span></td>
            <td>{{ $d->prescription->drug_name }} {{ $d->prescription->dose }}</td>
            <td>{{ $d->quantity }}</td>
            <td style="font-size:12px;">{{ $d->batch_lot ?: '—' }}</td>
            <td>{{ $d->price ? 'NGN ' . number_format($d->price) : '—' }}</td>
            <td style="font-size:12px;">{{ $d->payment_method ?: '—' }}</td>
            <td style="font-size:12px;color:var(--grey-500);">{{ $d->created_at->format('d M Y') }}</td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;color:var(--grey-500);padding:24px;">No dispenses on record.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $dispenses->links() }}</div>
</div>
@endsection
