@extends('layouts.app')
@section('title','Vitals Diary')
@section('topbar-title','Vitals Diary')

@section('content')
<div class="page-header-row">
  <div><h1>Vitals Diary</h1><p>Track your daily health measurements.</p></div>
</div>

{{-- Latest readings --}}
@if($latest)
<div class="stat-grid stat-grid-sm mb-24">
  @if($latest->bp_systolic)
  <div class="stat-card"><div class="stat-label">Blood Pressure</div><div class="stat-value stat-value-sm">{{ $latest->bp_systolic }}/{{ $latest->bp_diastolic }}</div><div class="stat-sub">mmHg</div><div class="stat-icon">❤️</div></div>
  @endif
  @if($latest->heart_rate)
  <div class="stat-card green"><div class="stat-label">Heart Rate</div><div class="stat-value stat-value-sm">{{ $latest->heart_rate }}</div><div class="stat-sub">bpm</div><div class="stat-icon">💓</div></div>
  @endif
  @if($latest->weight_kg)
  <div class="stat-card"><div class="stat-label">Weight</div><div class="stat-value stat-value-sm">{{ $latest->weight_kg }}</div><div class="stat-sub">kg</div><div class="stat-icon">⚖️</div></div>
  @endif
  @if($latest->glucose_mmol)
  <div class="stat-card amber"><div class="stat-label">Blood Glucose</div><div class="stat-value stat-value-sm">{{ $latest->glucose_mmol }}</div><div class="stat-sub">mmol/L</div><div class="stat-icon">🩸</div></div>
  @endif
  @if($latest->oxygen_saturation)
  <div class="stat-card"><div class="stat-label">SpO₂</div><div class="stat-value stat-value-sm">{{ $latest->oxygen_saturation }}%</div><div class="stat-sub">Oxygen sat.</div><div class="stat-icon">🫁</div></div>
  @endif
</div>
@endif

<div class="dash-grid-1-1 mb-24">
  {{-- Log form --}}
  <div class="card">
    <div class="card-header"><div class="card-title">Log New Reading</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('patient.vitals.store') }}">
        @csrf
        <div class="form-field mb-12">
          <label>Date &amp; Time *</label>
          <input type="datetime-local" name="logged_at" value="{{ old('logged_at', now()->format('Y-m-d\TH:i')) }}" required>
          @error('logged_at')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-grid mb-12">
          <div class="form-field">
            <label>BP Systolic (mmHg)</label>
            <input type="number" name="bp_systolic" value="{{ old('bp_systolic') }}" min="50" max="300" placeholder="120">
          </div>
          <div class="form-field">
            <label>BP Diastolic (mmHg)</label>
            <input type="number" name="bp_diastolic" value="{{ old('bp_diastolic') }}" min="30" max="200" placeholder="80">
          </div>
          <div class="form-field">
            <label>Heart Rate (bpm)</label>
            <input type="number" name="heart_rate" value="{{ old('heart_rate') }}" min="20" max="300" placeholder="72">
          </div>
          <div class="form-field">
            <label>Temperature (°C)</label>
            <input type="number" name="temperature_c" step="0.1" value="{{ old('temperature_c') }}" min="30" max="45" placeholder="36.6">
          </div>
          <div class="form-field">
            <label>Weight (kg)</label>
            <input type="number" name="weight_kg" step="0.1" value="{{ old('weight_kg') }}" min="1" max="500" placeholder="70.0">
          </div>
          <div class="form-field">
            <label>Blood Glucose (mmol/L)</label>
            <input type="number" name="glucose_mmol" step="0.1" value="{{ old('glucose_mmol') }}" min="1" max="50" placeholder="5.5">
          </div>
          <div class="form-field">
            <label>SpO₂ (%)</label>
            <input type="number" name="oxygen_saturation" value="{{ old('oxygen_saturation') }}" min="50" max="100" placeholder="98">
          </div>
          <div class="form-field">
            <label>Pain Level (0–10)</label>
            <input type="number" name="pain_level" value="{{ old('pain_level') }}" min="0" max="10" placeholder="0">
          </div>
        </div>
        <div class="form-field mb-16">
          <label>Notes</label>
          <textarea name="notes" rows="2" placeholder="e.g. after exercise, fasting…">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Reading</button>
      </form>
    </div>
  </div>

  {{-- BP chart --}}
  <div class="card">
    <div class="card-header"><div class="card-title">Blood Pressure Trend</div><div class="card-sub">Last 30 readings</div></div>
    <div class="card-body"><canvas id="bpChart" height="200"></canvas></div>
  </div>
</div>

{{-- History table --}}
<div class="card">
  <div class="card-header"><div class="card-title">History</div></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date/Time</th><th>BP</th><th>HR</th><th>Temp</th><th>Weight</th><th>Glucose</th><th>SpO₂</th><th>Pain</th><th></th></tr></thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td class="td-date">{{ $log->logged_at->format('d M Y, H:i') }}</td>
          <td>{{ $log->bp ?: '—' }}</td>
          <td>{{ $log->heart_rate ? $log->heart_rate.' bpm' : '—' }}</td>
          <td>{{ $log->temperature_c ? $log->temperature_c.'°C' : '—' }}</td>
          <td>{{ $log->weight_kg ? $log->weight_kg.' kg' : '—' }}</td>
          <td>{{ $log->glucose_mmol ? $log->glucose_mmol.' mmol/L' : '—' }}</td>
          <td>{{ $log->oxygen_saturation ? $log->oxygen_saturation.'%' : '—' }}</td>
          <td>{{ $log->pain_level !== null ? $log->pain_level.'/10' : '—' }}</td>
          <td>
            <form method="POST" action="{{ route('patient.vitals.destroy', $log->id) }}"
                  data-confirm="Delete this reading?"
                  data-confirm-ok="Delete">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-ghost btn-sm">✕</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="9"><div class="empty-state empty-state-sm"><div class="empty-icon">📊</div><p>No readings logged yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($logs->hasPages())<div class="pager-row">{{ $logs->links() }}</div>@endif
</div>

{{-- Chart data for vitals.js (not executed — CSP-safe) --}}
<script type="application/json" id="bp-chart-data">
{!! json_encode($chartData->map(fn($l) => [
  'label' => $l->logged_at->format('d M H:i'),
  'sys'   => $l->bp_systolic,
  'dia'   => $l->bp_diastolic,
])->values()) !!}
</script>
@endsection

@push('scripts')
@vite('resources/js/pages/vitals.js')
@endpush
