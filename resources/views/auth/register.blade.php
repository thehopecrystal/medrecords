@extends('layouts.auth')
@section('title', 'Create Account')
@section('content')

{{-- Progress bar --}}
<div class="progress-bar">
  <div class="progress-step active" id="prog-1">
    <div class="step-dot">1</div>
    <div class="step-label">Account</div>
  </div>
  <div class="progress-line" id="line-1"></div>
  <div class="progress-step" id="prog-2">
    <div class="step-dot">2</div>
    <div class="step-label">Health profile</div>
  </div>
  <div class="progress-line" id="line-2"></div>
  <div class="progress-step" id="prog-3">
    <div class="step-dot">3</div>
    <div class="step-label">Review</div>
  </div>
</div>

@if($errors->any())
  <div class="alert-error" id="serverErrors">
    @foreach($errors->all() as $e)<span>{{ $e }}</span><br>@endforeach
  </div>
@endif

<form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
  @csrf

  {{-- ── STEP 1: Account ──────────────────────────────── --}}
  <div class="wizard-step" id="step1">
    <h2>Create your account</h2>
    <p class="subtitle">Your secure health record, free forever</p>

    <div class="form-field">
      <label>Full name <span class="req">*</span></label>
      <input type="text" name="name" id="f-name"
             value="{{ old('name') }}"
             placeholder="Adaeze Okonkwo"
             autocomplete="name" required>
      <div class="field-hint">As it appears on government-issued ID</div>
    </div>
    <div class="form-field">
      <label>Email address <span class="req">*</span></label>
      <input type="email" name="email" id="f-email"
             value="{{ old('email') }}"
             placeholder="adaeze@gmail.com"
             autocomplete="email" required>
    </div>
    <div class="form-field">
      <label>Phone number <span class="req">*</span></label>
      <div class="input-prefix-wrap">
        <span class="input-prefix">🇳🇬 +234</span>
        <input type="tel" name="phone" id="f-phone"
               value="{{ old('phone') }}"
               placeholder="08045678901"
               autocomplete="tel"
               pattern="0[789][01]\d{8}"
               maxlength="11" required>
      </div>
      <div class="field-hint">11-digit Nigerian number e.g. 08045678901</div>
    </div>
    <div class="form-field">
      <label>Password <span class="req">*</span></label>
      <div class="input-with-action">
        <input type="password" name="password" id="f-password"
               placeholder="Minimum 8 characters"
               autocomplete="new-password"
               minlength="8" required>
        <button type="button" class="input-action" onclick="togglePwd('f-password','eye1')" title="Show password">
          <span id="eye1">👁</span>
        </button>
      </div>
      <div class="strength-bar" id="strengthBar"><div class="strength-fill" id="strengthFill"></div></div>
      <div class="field-hint" id="strengthLabel">Enter a password</div>
    </div>
    <div class="form-field">
      <label>Confirm password <span class="req">*</span></label>
      <div class="input-with-action">
        <input type="password" name="password_confirmation" id="f-confirm"
               placeholder="Repeat your password"
               autocomplete="new-password"
               minlength="8" required>
        <button type="button" class="input-action" onclick="togglePwd('f-confirm','eye2')" title="Show password">
          <span id="eye2">👁</span>
        </button>
      </div>
      <div class="field-hint match-hint" id="matchHint"></div>
    </div>

    <button type="button" class="btn-submit" onclick="goStep2()">Continue →</button>

    <div class="form-footer">
      Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
  </div>

  {{-- ── STEP 2: Health Profile ───────────────────────── --}}
  <div class="wizard-step" id="step2" style="display:none;">
    <h2>Your health profile</h2>
    <p class="subtitle">Helps providers give you better care — all optional</p>

    <div class="form-row">
      <div class="form-field">
        <label>Date of birth</label>
        <input type="date" name="dob" id="f-dob" value="{{ old('dob') }}"
               max="{{ date('Y-m-d', strtotime('-1 day')) }}">
      </div>
      <div class="form-field">
        <label>Gender</label>
        <select name="gender" id="f-gender">
          <option value="">Select…</option>
          <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
          <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
          <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other / prefer not to say</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label>Blood type</label>
        <select name="blood_type" id="f-blood">
          <option value="">Unknown</option>
          @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
            <option value="{{ $bt }}" {{ old('blood_type') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-field">
        <label>Genotype</label>
        <select name="genotype" id="f-genotype">
          <option value="">Unknown</option>
          @foreach(['AA','AS','SS','AC','SC'] as $g)
            <option value="{{ $g }}" {{ old('genotype') === $g ? 'selected' : '' }}>{{ $g }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-field">
      <label>State of residence</label>
      <select name="state" id="f-state">
        <option value="">Select state…</option>
        @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
          <option value="{{ $st }}" {{ old('state') === $st ? 'selected' : '' }}>{{ $st }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-field">
      <label>NIN (National Identity Number)</label>
      <input type="text" name="nin" id="f-nin"
             value="{{ old('nin') }}"
             placeholder="12345678901"
             maxlength="11"
             pattern="\d{11}"
             inputmode="numeric">
      <div class="field-hint">11-digit NIN — optional, used for identity verification</div>
    </div>

    <div style="display:flex;gap:10px;margin-top:4px;">
      <button type="button" class="btn-back" onclick="goStep1()">← Back</button>
      <button type="button" class="btn-submit" style="flex:1;" onclick="goStep3()">Review →</button>
    </div>
  </div>

  {{-- ── STEP 3: Review & Submit ──────────────────────── --}}
  <div class="wizard-step" id="step3" style="display:none;">
    <h2>Review & confirm</h2>
    <p class="subtitle">Check your details before creating your account</p>

    <div class="review-card">
      <div class="review-section">Account</div>
      <div class="review-row"><span>Name</span><strong id="rv-name">—</strong></div>
      <div class="review-row"><span>Email</span><strong id="rv-email">—</strong></div>
      <div class="review-row"><span>Phone</span><strong id="rv-phone">—</strong></div>
      <div class="review-row"><span>Password</span><strong>••••••••</strong></div>
    </div>

    <div class="review-card">
      <div class="review-section">Health profile</div>
      <div class="review-row"><span>Date of birth</span><strong id="rv-dob">Not provided</strong></div>
      <div class="review-row"><span>Gender</span><strong id="rv-gender">Not provided</strong></div>
      <div class="review-row"><span>Blood type</span><strong id="rv-blood">Unknown</strong></div>
      <div class="review-row"><span>Genotype</span><strong id="rv-genotype">Unknown</strong></div>
      <div class="review-row"><span>State</span><strong id="rv-state">Not provided</strong></div>
      <div class="review-row"><span>NIN</span><strong id="rv-nin">Not provided</strong></div>
    </div>

    <div class="form-field" style="margin-top:14px;">
      <label class="checkbox-label">
        <input type="checkbox" name="terms" id="termsBox" required
               style="width:auto;accent-color:var(--navy);cursor:pointer;margin-right:6px;">
        I agree to the <a href="#" style="color:var(--navy);">Terms of Service</a> and
        <a href="#" style="color:var(--navy);">Privacy Policy</a>. My health data is stored
        securely and shared only with providers I explicitly consent to.
      </label>
    </div>

    <div style="display:flex;gap:10px;margin-top:4px;">
      <button type="button" class="btn-back" onclick="goStep2()">← Back</button>
      <button type="submit" class="btn-submit" style="flex:1;" id="submitBtn">Create account</button>
    </div>

    <div class="form-footer" style="margin-top:14px;">
      Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
  </div>

</form>

<style>
/* ─── Auth card override ─────────────────────────── */
.auth-card { max-width: 480px; }

/* ─── Progress bar ───────────────────────────────── */
.progress-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 28px;
  gap: 0;
}
.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.step-dot {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: var(--grey-200);
  color: var(--grey-500);
  font-size: 12px;
  font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.2s, color 0.2s;
}
.step-label {
  font-size: 10px;
  font-weight: 600;
  color: var(--grey-500);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  white-space: nowrap;
  transition: color 0.2s;
}
.progress-step.active .step-dot { background: var(--navy); color: #fff; }
.progress-step.active .step-label { color: var(--navy); }
.progress-step.done .step-dot { background: var(--green); color: #fff; }
.progress-step.done .step-dot::after { content: '✓'; }
.progress-line {
  flex: 1;
  height: 2px;
  background: var(--grey-200);
  min-width: 28px;
  margin: 0 4px;
  margin-bottom: 18px;
  transition: background 0.2s;
}
.progress-line.done { background: var(--green); }

/* ─── Form helpers ───────────────────────────────── */
.req { color: var(--red); }
.field-hint {
  font-size: 11px;
  color: var(--grey-500);
  margin-top: 4px;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.input-prefix-wrap { display: flex; align-items: center; }
.input-prefix {
  background: var(--grey-100);
  border: 1px solid var(--grey-200);
  border-right: none;
  border-radius: 8px 0 0 8px;
  padding: 10px 10px;
  font-size: 12px;
  color: var(--grey-500);
  white-space: nowrap;
}
.input-prefix-wrap input {
  border-radius: 0 8px 8px 0 !important;
  flex: 1;
}
.input-with-action { position: relative; display: flex; align-items: center; }
.input-with-action input { padding-right: 40px; }
.input-action {
  position: absolute; right: 10px;
  background: none; border: none; cursor: pointer;
  font-size: 15px; line-height: 1; color: var(--grey-500); padding: 2px;
}

/* ─── Password strength ──────────────────────────── */
.strength-bar {
  height: 3px;
  background: var(--grey-200);
  border-radius: 3px;
  margin-top: 6px;
  overflow: hidden;
}
.strength-fill {
  height: 100%;
  width: 0;
  border-radius: 3px;
  transition: width 0.3s, background 0.3s;
  background: var(--red);
}
.match-hint { min-height: 14px; }

/* ─── Review card ────────────────────────────────── */
.review-card {
  border: 1px solid var(--grey-200);
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 12px;
}
.review-section {
  background: var(--grey-50);
  padding: 7px 14px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--grey-500);
}
.review-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 7px 14px;
  font-size: 13px;
  border-top: 1px solid var(--grey-100);
}
.review-row span { color: var(--grey-500); }
.review-row strong { text-align: right; max-width: 55%; word-break: break-all; }

/* ─── Buttons ────────────────────────────────────── */
.btn-back {
  background: var(--grey-100);
  color: var(--grey-900, #1A1F26);
  border: 1px solid var(--grey-200);
  border-radius: 10px;
  padding: 11px 18px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s;
}
.btn-back:hover { background: var(--grey-200); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 0;
  font-size: 12px;
  color: var(--grey-500);
  line-height: 1.5;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 400;
  cursor: pointer;
}
</style>

<script>
/* ── Helpers ─────────────────────────────────────── */
function togglePwd(inputId, iconId) {
  const el = document.getElementById(inputId);
  const ic = document.getElementById(iconId);
  el.type = el.type === 'password' ? 'text' : 'password';
  ic.textContent = el.type === 'password' ? '👁' : '🙈';
}

function setProgress(step) {
  [1,2,3].forEach(function(n) {
    const el = document.getElementById('prog-' + n);
    el.classList.remove('active','done');
    if (n < step) el.classList.add('done');
    if (n === step) el.classList.add('active');
    const line = document.getElementById('line-' + n);
    if (line) line.classList.toggle('done', n < step);
  });
}

function showStep(n) {
  [1,2,3].forEach(function(i) {
    document.getElementById('step' + i).style.display = i === n ? '' : 'none';
  });
  setProgress(n);
  window.scrollTo(0, 0);
}

/* ── Password strength ───────────────────────────── */
document.getElementById('f-password').addEventListener('input', function() {
  const val = this.value;
  const fill = document.getElementById('strengthFill');
  const label = document.getElementById('strengthLabel');
  let score = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const pct   = ['0%','20%','40%','65%','85%','100%'][score];
  const color = ['#ccc', '#B23A48','#E8A030','#B8860B','#4CAF50','#2C5F2D'][score];
  const text  = ['Enter a password','Too short','Weak','Fair','Good','Strong'][score];
  fill.style.width = pct;
  fill.style.background = color;
  label.textContent = text;
});

/* ── Password match ──────────────────────────────── */
function checkMatch() {
  const hint = document.getElementById('matchHint');
  const pw   = document.getElementById('f-password').value;
  const cf   = document.getElementById('f-confirm').value;
  if (!cf) { hint.textContent = ''; return true; }
  const ok = pw === cf;
  hint.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
  hint.style.color = ok ? 'var(--green)' : 'var(--red)';
  return ok;
}
document.getElementById('f-password').addEventListener('input', checkMatch);
document.getElementById('f-confirm').addEventListener('input', checkMatch);

/* ── Step 1 → 2 ──────────────────────────────────── */
function goStep2() {
  const name  = document.getElementById('f-name').value.trim();
  const email = document.getElementById('f-email').value.trim();
  const phone = document.getElementById('f-phone').value.trim();
  const pw    = document.getElementById('f-password').value;
  const cf    = document.getElementById('f-confirm').value;

  if (!name)  { alert('Please enter your full name.'); document.getElementById('f-name').focus(); return; }
  if (!email) { alert('Please enter your email address.'); document.getElementById('f-email').focus(); return; }
  if (!phone || phone.length !== 11 || !/^0[789][01]\d{8}$/.test(phone)) {
    alert('Please enter a valid 11-digit Nigerian phone number (e.g. 08045678901).');
    document.getElementById('f-phone').focus(); return;
  }
  if (pw.length < 8) { alert('Password must be at least 8 characters.'); document.getElementById('f-password').focus(); return; }
  if (!checkMatch())  { alert('Passwords do not match.'); document.getElementById('f-confirm').focus(); return; }

  showStep(2);
}

/* ── Step 2 → 3 ──────────────────────────────────── */
function goStep3() {
  const nin = document.getElementById('f-nin').value.trim();
  if (nin && !/^\d{11}$/.test(nin)) {
    alert('NIN must be exactly 11 digits or left blank.');
    document.getElementById('f-nin').focus(); return;
  }

  // Fill review
  document.getElementById('rv-name').textContent  = document.getElementById('f-name').value.trim() || '—';
  document.getElementById('rv-email').textContent = document.getElementById('f-email').value.trim() || '—';
  document.getElementById('rv-phone').textContent = document.getElementById('f-phone').value.trim() || '—';

  const dob   = document.getElementById('f-dob').value;
  const gdr   = document.getElementById('f-gender');
  const blood = document.getElementById('f-blood');
  const geno  = document.getElementById('f-genotype');
  const state = document.getElementById('f-state');

  document.getElementById('rv-dob').textContent     = dob ? formatDate(dob) : 'Not provided';
  document.getElementById('rv-gender').textContent  = gdr.options[gdr.selectedIndex].text !== 'Select…' ? gdr.options[gdr.selectedIndex].text : 'Not provided';
  document.getElementById('rv-blood').textContent   = blood.value || 'Unknown';
  document.getElementById('rv-genotype').textContent= geno.value  || 'Unknown';
  document.getElementById('rv-state').textContent   = state.value || 'Not provided';
  document.getElementById('rv-nin').textContent     = nin ? nin.replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3') : 'Not provided';

  showStep(3);
}

/* ── Step 2 ← 1 / Step 3 ← 2 ────────────────────── */
function goStep1() { showStep(1); }
function goStep2b() { showStep(2); }

/* ── Date formatter ──────────────────────────────── */
function formatDate(str) {
  const d = new Date(str + 'T00:00:00');
  return d.toLocaleDateString('en-NG', { day: 'numeric', month: 'long', year: 'numeric' });
}

/* ── Submit guard ────────────────────────────────── */
document.getElementById('regForm').addEventListener('submit', function(e) {
  const terms = document.getElementById('termsBox');
  if (!terms.checked) {
    e.preventDefault();
    alert('Please accept the Terms of Service to continue.');
    return;
  }
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = 'Creating account…';
});

/* ── Re-open correct step on server validation error ── */
@if($errors->any())
  @if($errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('password'))
    showStep(1);
  @elseif($errors->has('dob') || $errors->has('gender') || $errors->has('blood_type') || $errors->has('nin'))
    showStep(2);
  @else
    showStep(1);
  @endif
@else
  showStep(1);
@endif
</script>

@endsection
