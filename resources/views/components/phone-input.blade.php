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
$selectedCode = '+234';
$selectedName = 'Nigeria';
$selectedPh   = '08012345678';
$localValue   = $value;

if ($value && str_starts_with($value, '+')) {
    $sorted = collect($countries)->sortByDesc(fn($c) => strlen($c['code']))->values();
    foreach ($sorted as $c) {
        if (str_starts_with($value, $c['code'])) {
            $selectedCode = $c['code'];
            $selectedName = $c['name'];
            $selectedPh   = $c['ph'];
            $localValue   = substr($value, strlen($c['code']));
            break;
        }
    }
}
@endphp

{{-- Hidden field — holds the normalised E.164 value submitted to the server --}}
<input type="hidden" name="{{ $name }}" id="{{ $id }}_hidden" value="{{ old($name, $value) }}">

<div class="pi-wrap" id="{{ $id }}_wrap">
  <div class="pi-row">

    {{-- Native country select --}}
    <select class="pi-country" id="{{ $id }}_country" aria-label="Country dial code">
      @foreach($countries as $c)
        <option
          value="{{ $c['code'] }}"
          data-ph="{{ $c['ph'] }}"
          data-name="{{ $c['name'] }}"
          {{ ($c['code'] === $selectedCode && $c['name'] === $selectedName) ? 'selected' : '' }}>
          {{ $c['flag'] }} {{ $c['name'] }} ({{ $c['code'] }})
        </option>
      @endforeach
    </select>

    {{-- Local number input --}}
    <input type="tel"
           id="{{ $id }}_local"
           class="pi-local"
           value="{{ $localValue }}"
           placeholder="{{ $selectedPh }}"
           autocomplete="tel"
           inputmode="tel"
           {{ $required ? 'required' : '' }}>
  </div>
</div>

<script nonce="{{ $cspNonce }}">
(function () {
  var uid     = {{ json_encode($id) }};
  var country = document.getElementById(uid + '_country');
  var local   = document.getElementById(uid + '_local');
  var hidden  = document.getElementById(uid + '_hidden');

  function buildE164(localVal, code) {
    var n = localVal.replace(/\D/g, '');
    if (!n) return '';
    if (n.charAt(0) === '0') n = n.substring(1);
    return code + n;
  }

  function syncHidden() {
    hidden.value = buildE164(local.value, country.value);
  }

  country.addEventListener('change', function () {
    var opt = country.options[country.selectedIndex];
    local.placeholder = opt.dataset.ph || '';
    syncHidden();
  });

  local.addEventListener('input', syncHidden);

  syncHidden();
})();
</script>
