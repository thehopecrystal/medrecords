@extends('layouts.app')
@section('title', 'Send Notification')
@section('topbar-title', 'Send Notification')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Send Notification</h1>
    <p>Compose and broadcast in-app and email notifications to your users.</p>
  </div>
</div>

<div class="notif-compose-grid">

  {{-- Compose form --}}
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Compose Notification</div>
        <div class="card-sub">Recipients receive both an in-app notification and an email.</div>
      </div>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.notifications.send') }}" id="notifForm">
        @csrf

        {{-- Audience --}}
        <div class="form-field mb-18">
          <label>Audience</label>
          <div class="audience-grid">
            @foreach(['all' => ['label'=>'All Users','icon'=>'👥'], 'role' => ['label'=>'By Role','icon'=>'🏷'], 'user' => ['label'=>'Specific User','icon'=>'👤']] as $val => $opt)
            <label class="audience-card {{ old('audience','all') === $val ? 'active' : '' }}">
              <input type="radio" name="audience" value="{{ $val }}" {{ old('audience','all') === $val ? 'checked' : '' }} class="input-hidden">
              <span class="audience-icon">{{ $opt['icon'] }}</span>
              <span class="audience-label">{{ $opt['label'] }}</span>
            </label>
            @endforeach
          </div>
        </div>

        {{-- Role selector --}}
        <div class="form-field mb-18 js-hidden" id="extra-role">
          <label>Role</label>
          <select name="role">
            <option value="">Select role…</option>
            @foreach(['patient'=>'Patient','provider'=>'Provider / Doctor','pharmacist'=>'Pharmacist'] as $r => $label)
            <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        {{-- Specific user --}}
        <div class="form-field mb-18 js-hidden" id="extra-user">
          <label>User email or MedID</label>
          <input type="text" name="user_identifier" value="{{ old('user_identifier') }}"
                 placeholder="patient@example.com or MR-XXXXXX-XXXXXX"
                 list="user-list">
          <datalist id="user-list">
            @foreach($users as $u)
            <option value="{{ $u->email }}">{{ $u->name }}</option>
            @endforeach
          </datalist>
          <div class="form-hint">Enter an email address or patient MedID.</div>
        </div>

        {{-- Title --}}
        <div class="form-field mb-18">
          <label>Notification Title *</label>
          <input type="text" name="title" value="{{ old('title') }}"
                 placeholder="System maintenance on Saturday, 14 June"
                 maxlength="120" required>
          <div class="form-hint">Appears as the subject line in email and bold heading in-app.</div>
        </div>

        {{-- Body --}}
        <div class="form-field mb-24">
          <label>Message *</label>
          <textarea name="body" rows="5" maxlength="2000" required
                    placeholder="Dear user, please be informed that…&#10;&#10;Thank you for using MedRecords.">{{ old('body') }}</textarea>
          <div class="form-hint form-hint-right" id="charCount">0 / 2000 characters</div>
        </div>

        <div class="alert navy mb-20">
          <span>ℹ</span>
          <span>Sending creates one in-app notification and one email per recipient. Large audiences may take a moment.</span>
        </div>

        <button type="submit" class="btn btn-primary btn-full" id="sendBtn">
          📣 Send Notification
        </button>
      </form>
    </div>
  </div>

  {{-- History --}}
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Broadcast History</div>
        <div class="card-sub">Recent admin notifications, newest first.</div>
      </div>
    </div>
    @if($rows->isEmpty())
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      <h3>No broadcasts yet</h3>
      <p>Sent notifications will appear here.</p>
    </div>
    @else
    <div class="table-wrap">
      <table>
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
              <div class="notif-table-title">{{ $n->title }}</div>
              <div class="notif-table-body">{{ $n->body }}</div>
            </td>
            <td>
              <span class="pill navy">{{ number_format($n->recipient_count) }}</span>
            </td>
            <td class="td-sm">{{ $n->sent_by_name }}</td>
            <td class="td-date">
              {{ $n->created_at->format('d M Y') }}<br>
              <span class="td-date-sub">{{ $n->created_at->format('H:i') }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

</div>

<script nonce="{{ $cspNonce }}">
document.querySelectorAll('.audience-card').forEach(function(card) {
  card.addEventListener('click', function() {
    document.querySelectorAll('.audience-card').forEach(c => c.classList.remove('active'));
    this.classList.add('active');
    var val = this.querySelector('input').value;
    document.getElementById('extra-role').classList.toggle('js-hidden', val !== 'role');
    document.getElementById('extra-user').classList.toggle('js-hidden', val !== 'user');
  });
});

(function() {
  var checked = document.querySelector('input[name="audience"]:checked');
  if (checked) {
    document.getElementById('extra-role').classList.toggle('js-hidden', checked.value !== 'role');
    document.getElementById('extra-user').classList.toggle('js-hidden', checked.value !== 'user');
  }
})();

var bodyEl  = document.querySelector('textarea[name="body"]');
var counter = document.getElementById('charCount');
function updateCount() { counter.textContent = bodyEl.value.length + ' / 2000 characters'; }
bodyEl.addEventListener('input', updateCount);
updateCount();

document.getElementById('notifForm').addEventListener('submit', function(e) {
  var aud = document.querySelector('input[name="audience"]:checked')?.value;
  if (aud === 'all' && !confirm('Send this notification to ALL active users? This will also email each one.')) {
    e.preventDefault();
  }
});
</script>
@endsection
