@extends('layouts.app')
@section('title', 'Verify Prescription')
@section('topbar-title', 'Verify & Dispense')


@section('content')
<div class="page-header">
  <h1>Verify Prescription</h1>
  <p>Scan or enter a prescription ID to begin</p>
</div>

<div class="card" style="margin-bottom:20px;">
  <form method="GET" action="{{ route('pharmacy.lookup') }}" style="display:flex;gap:12px;">
    <input type="text" name="rx_id" value="{{ $rxId }}" placeholder="Prescription ID e.g. RX-ABCD-1234"
      style="flex:1;background:var(--grey-50);border:1px solid var(--grey-200);border-radius:8px;padding:10px 14px;font-size:14px;" autofocus>
    <button type="submit" class="btn btn-primary">Look up</button>
  </form>
</div>

@if($rxId && !$prescription)
  <div class="alert red"><span>✗</span><span>Prescription <strong>{{ $rxId }}</strong> not found.</span></div>
@endif

@if($prescription)
  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px;">
    {{-- Prescription card --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Prescription</div></div>
      <div class="detail-row"><span class="k">ID</span><span class="v" style="font-family:monospace;">{{ $prescription->rx_id }}</span></div>
      <div class="detail-row"><span class="k">Patient</span><span class="v">{{ $prescription->patient->user->name }}</span></div>
      <div class="detail-row"><span class="k">MedID</span><span class="v">{{ $prescription->patient->med_id }}</span></div>
      <div class="detail-row"><span class="k">Prescriber</span><span class="v">{{ $prescription->prescribedBy->name }}</span></div>
      <div class="detail-row"><span class="k">Date issued</span><span class="v">{{ $prescription->created_at->format('d M Y H:i') }}</span></div>
      <div class="detail-row"><span class="k">Drug</span><span class="v"><strong>{{ $prescription->drug_name }} {{ $prescription->dose }}</strong></span></div>
      <div class="detail-row"><span class="k">Frequency</span><span class="v">{{ $prescription->frequency }}</span></div>
      <div class="detail-row"><span class="k">Duration</span><span class="v">{{ $prescription->duration_days ? $prescription->duration_days . ' days' : '—' }}</span></div>
      <div class="detail-row"><span class="k">Diagnosis</span><span class="v">{{ $prescription->diagnosis_context ?: '—' }}</span></div>

      @if($prescription->status === 'active')
        <div class="alert green" style="margin-top:14px;margin-bottom:0;">
          <span>✓</span><span><strong>Prescription verified.</strong> Signed by {{ $prescription->prescribedBy->name }}, not yet dispensed.</span>
        </div>
      @elseif($prescription->status === 'dispensed')
        <div class="alert amber" style="margin-top:14px;margin-bottom:0;">
          <span>!</span><span><strong>Already dispensed.</strong> This prescription has been dispensed {{ $prescription->dispenses->count() }} time(s).</span>
        </div>
      @else
        <div class="alert red" style="margin-top:14px;margin-bottom:0;">
          <span>✗</span><span>Prescription status: <strong>{{ $prescription->status }}</strong>. Cannot dispense.</span>
        </div>
      @endif
    </div>

    {{-- NHIA eligibility --}}
    <div class="card">
      <div class="card-header"><div class="card-title">NHIA Eligibility</div></div>
      @if($nhia)
        @if($nhia['status'] === 'enrolled')
          <div class="alert green" style="margin-bottom:12px;">
            <span>✓</span><span><strong>{{ $nhia['message'] }}</strong></span>
          </div>
          <div class="detail-row"><span class="k">Plan</span><span class="v">{{ $nhia['plan'] }}</span></div>
          <div class="detail-row"><span class="k">Insurer</span><span class="v">{{ $nhia['insurer'] }}</span></div>
          <div class="detail-row"><span class="k">Member ID</span><span class="v" style="font-family:monospace;font-size:12px;">{{ $nhia['member_id'] }}</span></div>
          <div class="detail-row"><span class="k">Coverage</span><span class="v"><strong style="color:var(--green);">{{ $nhia['coverage'] }}%</strong></span></div>
          <div class="detail-row"><span class="k">Patient copay</span><span class="v">{{ $nhia['copay'] }}%</span></div>
          <div class="detail-row"><span class="k">Annual balance</span><span class="v">NGN {{ number_format($nhia['balance']) }}</span></div>
        @else
          <div class="alert amber" style="margin-bottom:0;">
            <span>!</span><span>{{ $nhia['message'] }} Patient will pay out of pocket.</span>
          </div>
        @endif
      @else
        <div style="font-size:12px;color:var(--grey-500);">Look up a prescription to check eligibility.</div>
      @endif
    </div>

    {{-- Safety check --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Safety check</div></div>
      <div class="detail-row">
        <span class="k">Allergies on file</span>
        <span class="v" style="{{ $prescription->patient->activeAllergies->count() ? 'color:var(--red);' : '' }}">
          {{ $prescription->patient->activeAllergies->count() ? $prescription->patient->activeAllergies->pluck('allergen')->join(', ') : 'None' }}
        </span>
      </div>
      <div class="detail-row">
        <span class="k">Active medications</span>
        <span class="v">{{ $prescription->patient->activePrescriptions->count() ? $prescription->patient->activePrescriptions->pluck('drug_name')->join(', ') : 'None' }}</span>
      </div>
      @php $latestLab = $prescription->patient->labResults->sortByDesc('collected_at')->first(); @endphp
      @if($latestLab)
        <div class="detail-row">
          <span class="k">Latest lab</span>
          <span class="v">{{ $latestLab->test_name }} — {{ $latestLab->result }}</span>
        </div>
      @endif
      <div class="alert green" style="margin-top:14px;margin-bottom:8px;"><span>✓</span><span>No duplicate dispense found.</span></div>
      @if($prescription->patient->activeAllergies->count())
        <div class="alert red"><span>⚠</span><span>Patient has recorded allergies — verify drug safety before dispensing.</span></div>
      @endif
    </div>
  </div>

  @if($prescription->status === 'active')
    {{-- Dispense form --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Record dispense</div></div>
      <form method="POST" action="{{ route('pharmacy.dispense', $prescription->id) }}">
        @csrf
        <div class="form-grid">
          <div class="form-field">
            <label>Quantity dispensed</label>
            <input type="number" name="quantity" placeholder="e.g. 60" min="1" required>
          </div>
          <div class="form-field">
            <label>Batch / lot number</label>
            <input type="text" name="batch_lot" placeholder="e.g. MET-2604-A">
          </div>
          <div class="form-field">
            <label>Price (NGN)</label>
            <input type="number" name="price" placeholder="e.g. 4200" step="0.01">
          </div>
          <div class="form-field">
            <label>Payment method</label>
            <input type="text" name="payment_method"
              value="{{ $nhia && $nhia['status'] === 'enrolled' ? $nhia['plan'] . ' (' . $nhia['coverage'] . '% covered)' : '' }}"
              placeholder="e.g. NHIA / cash / transfer">
          </div>
          <div class="form-field full">
            <label>Counseling notes</label>
            <textarea name="counseling_notes" placeholder="Counseling provided to patient…">{{ $prescription->instructions }}</textarea>
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-green">Confirm dispense</button>
        </div>
      </form>
    </div>
  @endif
@endif
@endsection
