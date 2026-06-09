@props([
    'name'     => 'phone',
    'id'       => 'phoneInput',
    'value'    => '',
    'required' => false,
])

@php
$countries = [
    // Africa — primary market
    ['name' => 'Nigeria',              'code' => '+234', 'flag' => '🇳🇬', 'ph' => '08012345678'],
    ['name' => 'Ghana',                'code' => '+233', 'flag' => '🇬🇭', 'ph' => '0241234567'],
    ['name' => 'Kenya',                'code' => '+254', 'flag' => '🇰🇪', 'ph' => '0712345678'],
    ['name' => 'South Africa',         'code' => '+27',  'flag' => '🇿🇦', 'ph' => '0812345678'],
    ['name' => 'Ethiopia',             'code' => '+251', 'flag' => '🇪🇹', 'ph' => '0912345678'],
    ['name' => 'Tanzania',             'code' => '+255', 'flag' => '🇹🇿', 'ph' => '0712345678'],
    ['name' => 'Uganda',               'code' => '+256', 'flag' => '🇺🇬', 'ph' => '0712345678'],
    ['name' => 'Rwanda',               'code' => '+250', 'flag' => '🇷🇼', 'ph' => '0712345678'],
    ['name' => 'Cameroon',             'code' => '+237', 'flag' => '🇨🇲', 'ph' => '671234567'],
    ['name' => 'Senegal',              'code' => '+221', 'flag' => '🇸🇳', 'ph' => '701234567'],
    ['name' => "Côte d'Ivoire",        'code' => '+225', 'flag' => '🇨🇮', 'ph' => '0712345678'],
    ['name' => 'Zambia',               'code' => '+260', 'flag' => '🇿🇲', 'ph' => '0971234567'],
    ['name' => 'Zimbabwe',             'code' => '+263', 'flag' => '🇿🇼', 'ph' => '0771234567'],
    ['name' => 'Angola',               'code' => '+244', 'flag' => '🇦🇴', 'ph' => '923456789'],
    ['name' => 'Mozambique',           'code' => '+258', 'flag' => '🇲🇿', 'ph' => '841234567'],
    ['name' => 'Egypt',                'code' => '+20',  'flag' => '🇪🇬', 'ph' => '01012345678'],
    ['name' => 'Morocco',              'code' => '+212', 'flag' => '🇲🇦', 'ph' => '0612345678'],
    ['name' => 'Tunisia',              'code' => '+216', 'flag' => '🇹🇳', 'ph' => '21234567'],
    ['name' => 'Sudan',                'code' => '+249', 'flag' => '🇸🇩', 'ph' => '0912345678'],
    ['name' => 'Mali',                 'code' => '+223', 'flag' => '🇲🇱', 'ph' => '71234567'],
    ['name' => 'Niger',                'code' => '+227', 'flag' => '🇳🇪', 'ph' => '91234567'],
    // Diaspora destinations
    ['name' => 'United Kingdom',       'code' => '+44',  'flag' => '🇬🇧', 'ph' => '07911123456'],
    ['name' => 'United States',        'code' => '+1',   'flag' => '🇺🇸', 'ph' => '2025551234'],
    ['name' => 'Canada',               'code' => '+1',   'flag' => '🇨🇦', 'ph' => '4165551234'],
    ['name' => 'France',               'code' => '+33',  'flag' => '🇫🇷', 'ph' => '0612345678'],
    ['name' => 'Germany',              'code' => '+49',  'flag' => '🇩🇪', 'ph' => '01512345678'],
    ['name' => 'Italy',                'code' => '+39',  'flag' => '🇮🇹', 'ph' => '3121234567'],
    ['name' => 'Spain',                'code' => '+34',  'flag' => '🇪🇸', 'ph' => '612345678'],
    ['name' => 'Netherlands',          'code' => '+31',  'flag' => '🇳🇱', 'ph' => '0612345678'],
    ['name' => 'Portugal',             'code' => '+351', 'flag' => '🇵🇹', 'ph' => '912345678'],
    ['name' => 'Ireland',              'code' => '+353', 'flag' => '🇮🇪', 'ph' => '0871234567'],
    ['name' => 'Norway',               'code' => '+47',  'flag' => '🇳🇴', 'ph' => '41234567'],
    ['name' => 'Sweden',               'code' => '+46',  'flag' => '🇸🇪', 'ph' => '0712345678'],
    ['name' => 'United Arab Emirates', 'code' => '+971', 'flag' => '🇦🇪', 'ph' => '501234567'],
    ['name' => 'Saudi Arabia',         'code' => '+966', 'flag' => '🇸🇦', 'ph' => '501234567'],
    ['name' => 'Qatar',                'code' => '+974', 'flag' => '🇶🇦', 'ph' => '33123456'],
    ['name' => 'India',                'code' => '+91',  'flag' => '🇮🇳', 'ph' => '9876543210'],
    ['name' => 'China',                'code' => '+86',  'flag' => '🇨🇳', 'ph' => '13912345678'],
    ['name' => 'Australia',            'code' => '+61',  'flag' => '🇦🇺', 'ph' => '0412345678'],
];

// Parse existing value back to country + local part.
// Supports both E.164 (+2348012345678) and legacy Nigerian (08012345678).
$selectedCode = '+234';
$selectedFlag = '🇳🇬';
$selectedName = 'Nigeria';
$selectedPh   = '08012345678';
$localValue   = $value;

if ($value && str_starts_with($value, '+')) {
    // Sort longest code first so +234 matches before +23
    $sorted = collect($countries)->sortByDesc(fn($c) => strlen($c['code']))->values();
    foreach ($sorted as $c) {
        if (str_starts_with($value, $c['code'])) {
            $selectedCode = $c['code'];
            $selectedFlag = $c['flag'];
            $selectedName = $c['name'];
            $selectedPh   = $c['ph'];
            $localValue   = substr($value, strlen($c['code']));
            break;
        }
    }
} elseif ($value && !str_starts_with($value, '+')) {
    // Legacy stored value (e.g. 08012345678) — keep it in the local field,
    // default country stays Nigeria; JS will normalise on next submit.
    $localValue = $value;
}
@endphp

{{-- Hidden field that holds the normalised E.164 value submitted to the server --}}
<input type="hidden" name="{{ $name }}" id="{{ $id }}_hidden" value="{{ old($name, $value) }}">

<div class="pi-wrap" id="{{ $id }}_wrap">
  <div class="pi-row">

    {{-- Country selector button --}}
    <button type="button" class="pi-trigger" id="{{ $id }}_trigger"
            aria-haspopup="listbox" aria-expanded="false" title="Select country code">
      <span class="pi-flag" id="{{ $id }}_flag">{{ $selectedFlag }}</span>
      <span class="pi-code" id="{{ $id }}_code">{{ $selectedCode }}</span>
      <svg class="pi-chevron" viewBox="0 0 10 6" width="9" height="6" aria-hidden="true">
        <path d="M0 0l5 6 5-6z" fill="currentColor"/>
      </svg>
    </button>

    {{-- Visible local number input --}}
    <input type="tel"
           id="{{ $id }}_local"
           class="pi-local"
           value="{{ $localValue }}"
           placeholder="{{ $selectedPh }}"
           autocomplete="tel"
           inputmode="tel"
           {{ $required ? 'required' : '' }}>
  </div>

  {{-- Dropdown --}}
  <div class="pi-dropdown" id="{{ $id }}_dropdown" style="display:none;" role="listbox" aria-label="Country dial code">
    <div class="pi-search-wrap">
      <input type="text" class="pi-search" id="{{ $id }}_search"
             placeholder="Search country…" autocomplete="off" spellcheck="false">
    </div>
    <div class="pi-list" id="{{ $id }}_list">
      @foreach($countries as $c)
      <div class="pi-item {{ ($c['code'] === $selectedCode && $c['name'] === $selectedName) ? 'pi-selected' : '' }}"
           data-code="{{ $c['code'] }}"
           data-flag="{{ $c['flag'] }}"
           data-name="{{ $c['name'] }}"
           data-ph="{{ $c['ph'] }}"
           role="option"
           aria-selected="{{ ($c['code'] === $selectedCode && $c['name'] === $selectedName) ? 'true' : 'false' }}">
        <span class="pi-item-flag">{{ $c['flag'] }}</span>
        <span class="pi-item-name">{{ $c['name'] }}</span>
        <span class="pi-item-code">{{ $c['code'] }}</span>
      </div>
      @endforeach
    </div>
  </div>
</div>

<style>
/* ── Phone input component ─────────────────────────── */
.pi-wrap  { position: relative; }
.pi-row   { display: flex; }

.pi-trigger {
  display: flex; align-items: center; gap: 5px;
  padding: 0 10px; height: 42px;
  background: #F8FAFC; border: 1.5px solid #D1D5DB;
  border-right: none; border-radius: 8px 0 0 8px;
  cursor: pointer; font-size: 13px; font-weight: 600;
  color: #374151; white-space: nowrap; flex-shrink: 0;
  font-family: inherit; transition: background .15s, border-color .15s;
  line-height: 1;
}
.pi-trigger:hover { background: #EFF3F8; border-color: #9CA3AF; }
.pi-trigger:focus-visible { outline: 2px solid #1e3a5f; outline-offset: 2px; }

.pi-flag { font-size: 20px; line-height: 1; }
.pi-code { font-size: 12px; font-weight: 700; letter-spacing: -.2px; }
.pi-chevron { opacity: .45; flex-shrink: 0; margin-left: 1px; }

.pi-local {
  flex: 1; min-width: 0;
  border-radius: 0 8px 8px 0 !important;
  border-left: none !important;
}
.pi-local:focus { z-index: 1; }

.pi-dropdown {
  position: absolute; top: calc(100% + 5px); left: 0;
  width: 300px; background: #fff;
  border: 1.5px solid #D1D5DB; border-radius: 10px;
  box-shadow: 0 8px 28px rgba(0,0,0,.13);
  z-index: 9999; overflow: hidden;
}

.pi-search-wrap { padding: 8px 8px 6px; }
.pi-search {
  width: 100%; box-sizing: border-box;
  border: 1.5px solid #E5E7EB; border-radius: 7px;
  padding: 7px 10px; font-size: 13px;
  font-family: inherit; outline: none; background: #F9FAFB;
}
.pi-search:focus { border-color: #1e3a5f; background: #fff; }

.pi-list { max-height: 230px; overflow-y: auto; }
.pi-list::-webkit-scrollbar { width: 5px; }
.pi-list::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 5px; }

.pi-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 13px; cursor: pointer; font-size: 13px;
  transition: background .1s; user-select: none;
}
.pi-item:hover    { background: #F0F4FF; }
.pi-item.pi-selected { background: #EFF6FF; }
.pi-item-flag { font-size: 19px; line-height: 1; flex-shrink: 0; }
.pi-item-name { flex: 1; color: #1F2937; }
.pi-item-code { color: #6B7280; font-size: 12px; font-weight: 600; flex-shrink: 0; }
</style>

<script>
(function () {
  var uid      = {{ json_encode($id) }};
  var wrap     = document.getElementById(uid + '_wrap');
  var trigger  = document.getElementById(uid + '_trigger');
  var dropdown = document.getElementById(uid + '_dropdown');
  var search   = document.getElementById(uid + '_search');
  var list     = document.getElementById(uid + '_list');
  var hidden   = document.getElementById(uid + '_hidden');
  var local    = document.getElementById(uid + '_local');
  var flagEl   = document.getElementById(uid + '_flag');
  var codeEl   = document.getElementById(uid + '_code');
  var currentCode = codeEl.textContent.trim();

  function buildE164(localVal, code) {
    var n = localVal.replace(/\D/g, '');
    if (!n) return '';
    // Strip a single leading zero (used as trunk prefix in many countries)
    if (n.charAt(0) === '0') n = n.substring(1);
    return code + n;
  }

  function syncHidden() {
    hidden.value = buildE164(local.value, currentCode);
  }

  // Toggle dropdown
  trigger.addEventListener('click', function (e) {
    e.preventDefault(); e.stopPropagation();
    var open = dropdown.style.display !== 'none';
    if (open) {
      closeDropdown();
    } else {
      dropdown.style.display = '';
      trigger.setAttribute('aria-expanded', 'true');
      search.value = '';
      filterList('');
      search.focus();
    }
  });

  function closeDropdown() {
    dropdown.style.display = 'none';
    trigger.setAttribute('aria-expanded', 'false');
  }

  // Close on outside click
  document.addEventListener('click', function (e) {
    if (!wrap.contains(e.target)) closeDropdown();
  });

  // Keyboard: Escape closes
  dropdown.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeDropdown(); trigger.focus(); }
  });

  // Select a country
  list.addEventListener('click', function (e) {
    var item = e.target.closest('.pi-item');
    if (!item) return;
    selectCountry(item);
  });

  function selectCountry(item) {
    currentCode = item.dataset.code;
    flagEl.textContent  = item.dataset.flag;
    codeEl.textContent  = item.dataset.code;
    local.placeholder   = item.dataset.ph;
    list.querySelectorAll('.pi-item').forEach(function (el) {
      el.classList.remove('pi-selected');
      el.setAttribute('aria-selected', 'false');
    });
    item.classList.add('pi-selected');
    item.setAttribute('aria-selected', 'true');
    closeDropdown();
    syncHidden();
    local.focus();
  }

  // Live search
  search.addEventListener('input', function () { filterList(this.value); });
  function filterList(q) {
    var lq = q.toLowerCase().trim();
    list.querySelectorAll('.pi-item').forEach(function (item) {
      var match = !lq
        || item.dataset.name.toLowerCase().includes(lq)
        || item.dataset.code.includes(lq);
      item.style.display = match ? '' : 'none';
    });
  }

  // Sync hidden whenever local input changes
  local.addEventListener('input', syncHidden);

  // Initialise hidden value immediately
  syncHidden();
})();
</script>
