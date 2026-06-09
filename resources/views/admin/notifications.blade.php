@extends('layouts.app')
@section('title', 'Send Notification')
@section('topbar-title', 'Send Notification')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Send Notification</h1>
    <p>Compose and broadcast in-app and email notifications to your users</p>
  </div>
</div>

@if(session('success'))
  <div class="alert green" style="margin-bottom:20px;">
    <span>✓</span><span>{{ session('success') }}</span>
  </div>
@endif
@if($errors->any())
  <div class="alert red" style="margin-bottom:20px;">
    <span>✗</span><span>{{ $errors->first() }}</span>
  </div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

  {{-- Compose form --}}
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Compose Notification</div>
        <div class="card-sub">Recipients receive both an in-app notification and an email</div>
      </div>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.notifications.send') }}" id="notifForm">
        @csrf

        {{-- Audience --}}
        <div class="form-field" style="margin-bottom:18px;">
          <label>Audience</label>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:6px;">
            @foreach(['all' => ['label'=>'All Users','icon'=>'👥'], 'role' => ['label'=>'By Role','icon'=>'🏷'], 'user' => ['label'=>'Specific User','icon'=>'👤']] as $val => $opt)
            <label class="audience-card {{ old('audience','all') === $val ? 'active' : '' }}">
              <input type="radio" name="audience" value="{{ $val }}" {{ old('audience','all') === $val ? 'checked' : '' }} style="display:none;">
              <span style="font-size:22px;display:block;margin-bottom:4px;">{{ $opt['icon'] }}</span>
              <span style="font-size:12px;font-weight:600;">{{ $opt['label'] }}</span>
            </label>
            @endforeach
          </div>
        </div>

        {{-- Role selector --}}
        <div class="form-field audience-extra" id="extra-role" style="display:none;margin-bottom:18px;">
          <label>Role</label>
          <select name="role">
            <option value="">Select role…</option>
            @foreach(['patient'=>'Patient','provider'=>'Provider / Doctor','pharmacist'=>'Pharmacist','facility_admin'=>'Facility Admin','super_admin'=>'Super Admin'] as $r => $label)
            <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        {{-- Specific user --}}
        <div class="form-field audience-extra" id="extra-user" style="display:none;margin-bottom:18px;">
          <label>User email or MedID</label>
          <input type="text" name="user_identifier" value="{{ old('user_identifier') }}"
                 placeholder="patient@example.com or MR-XXXXXX-XXXXXX"
                 list="user-list">
          <datalist id="user-list">
            @foreach($users as $u)
            <option value="{{ $u->email }}">{{ $u->name }}</option>
            @endforeach
          </datalist>
          <div class="form-hint">Enter an email address or patient MedID</div>
        </div>

        {{-- Title --}}
        <div class="form-field" style="margin-bottom:18px;">
          <label>Notification Title *</label>
          <input type="text" name="title" value="{{ old('title') }}"
                 placeholder="System maintenance on Saturday, 14 June"
                 maxlength="120" required>
          <div class="form-hint">Appears as the subject line in email and bold heading in-app</div>
        </div>

        {{-- Body --}}
        <div class="form-field" style="margin-bottom:24px;">
          <label>Message *</label>
          <textarea name="body" rows="5" maxlength="2000" required
                    placeholder="Dear user, please be informed that…&#10;&#10;Thank you for using MedRecords.">{{ old('body') }}</textarea>
          <div class="form-hint" id="charCount" style="text-align:right;">0 / 2000 characters</div>
        </div>

        <div class="alert navy" style="margin-bottom:20px;">
          <span>ℹ</span>
          <span>Sending will create one in-app notification and one email per recipient. Large audiences may take a moment to process.</span>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;" id="sendBtn">
          Send Notification
        </button>
      </form>
    </div>
  </div>

  {{-- History --}}
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Broadcast History</div>
        <div class="card-sub">Recent admin notifications, newest first</div>
      </div>
    </div>
    @if($rows->isEmpty())
    <div class="card-body" style="text-align:center;padding:48px 24px;color:var(--text-2);">
      <div style="font-size:36px;margin-bottom:10px;">📭</div>
      <div style="font-weight:600;">No broadcasts yet</div>
      <div style="font-size:13px;margin-top:4px;">Sent notifications will appear here</div>
    </div>
    @else
    <div style="overflow:hidden;">
      <table style="width:100%;">
        <thead>
          <tr>
            <th>Title</th>
            <th>Recipients</th>
            <th>Sent by</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $n)
          <tr>
            <td>
              <div style="font-weight:600;color:var(--text-1);margin-bottom:2px;">{{ $n->title }}</div>
              <div style="font-size:12px;color:var(--text-2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;">
                {{ $n->body }}
              </div>
            </td>
            <td>
              <span class="pill navy">{{ number_format($n->recipient_count) }}</span>
            </td>
            <td style="font-size:13px;">{{ $n->sent_by_name }}</td>
            <td style="font-size:12px;color:var(--text-2);white-space:nowrap;">
              {{ $n->created_at->format('d M Y') }}<br>
              <span style="opacity:.7;">{{ $n->created_at->format('H:i') }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

</div>

<style nonce="{{ $cspNonce }}">
.audience-card {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 14px 8px; border: 2px solid var(--border); border-radius: 10px;
  cursor: pointer; transition: all 0.15s; text-align: center; gap: 2px;
}
.audience-card:hover { border-color: var(--navy); background: var(--navy-lt); }
.audience-card.active { border-color: var(--navy); background: var(--navy); color: #fff; }
</style>

<script nonce="{{ $cspNonce }}">
// Audience card toggle
document.querySelectorAll('.audience-card').forEach(function(card) {
  card.addEventListener('click', function() {
    document.querySelectorAll('.audience-card').forEach(c => c.classList.remove('active'));
    this.classList.add('active');
    var val = this.querySelector('input').value;
    document.getElementById('extra-role').style.display = val === 'role' ? '' : 'none';
    document.getElementById('extra-user').style.display = val === 'user' ? '' : 'none';
  });
});

// Restore visibility on page load (validation error re-render)
(function() {
  var checked = document.querySelector('input[name="audience"]:checked');
  if (checked) {
    document.getElementById('extra-role').style.display = checked.value === 'role' ? '' : 'none';
    document.getElementById('extra-user').style.display = checked.value === 'user' ? '' : 'none';
  }
})();

// Character counter
var bodyEl = document.querySelector('textarea[name="body"]');
var counter = document.getElementById('charCount');
function updateCount() { counter.textContent = bodyEl.value.length + ' / 2000 characters'; }
bodyEl.addEventListener('input', updateCount);
updateCount();

// Confirm before sending to all
document.getElementById('notifForm').addEventListener('submit', function(e) {
  var aud = document.querySelector('input[name="audience"]:checked')?.value;
  if (aud === 'all') {
    if (!confirm('Send this notification to ALL active users? This will also email each one.')) {
      e.preventDefault();
    }
  }
});
</script>
@endsection
