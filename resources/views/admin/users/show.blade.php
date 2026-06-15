@extends('layouts.app')
@section('title', 'User: '.$user->name)
@section('topbar-title', 'User Detail')

@section('content')
@php
  $roleColors = ['patient'=>'navy','provider'=>'green','pharmacist'=>'green'];
  $roleLabels = ['patient'=>'Patient','provider'=>'Provider','pharmacist'=>'Pharmacist'];
@endphp

<div class="page-header-row">
  <div>
    <h1>{{ $user->name }}</h1>
    <p>
      <span class="pill {{ $roleColors[$user->role] ?? 'grey' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span>
      &nbsp;
      @if($user->is_active)
        <span class="pill green">● Active</span>
      @else
        <span class="pill red">● Inactive</span>
      @endif
    </p>
  </div>
  <div class="btn-group-row">
    @if($user->id !== auth()->id())
      <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" class="form-inline"
            data-confirm="{{ $user->is_active ? 'Deactivate' : 'Activate' }} {{ $user->name }}?"
            data-confirm-btn="{{ $user->is_active ? 'btn-danger' : 'btn-green' }}"
            data-confirm-ok="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn {{ $user->is_active ? 'btn-danger' : 'btn-green' }} btn-sm">
          {{ $user->is_active ? '⊘ Deactivate' : '✓ Activate' }}
        </button>
      </form>
      <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="form-inline"
            data-confirm="Permanently delete {{ $user->name }}? This cannot be undone."
            data-confirm-title="Delete User"
            data-confirm-ok="Delete">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
      </form>
    @endif
    <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
</div>

<div class="page-2col-grid">

  {{-- Edit profile --}}
  <div class="card">
    <div class="card-header"><div class="card-title">Edit Profile</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf @method('PUT')
        <div class="form-field mb-12">
          <label>Full Name</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
          @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-field mb-12">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
          @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-field mb-12">
          <label>Phone</label>
          <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required>
          @error('phone')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-field mb-20">
          <label>Role</label>
          <select name="role" required>
            @foreach(['patient'=>'Patient','provider'=>'Provider','pharmacist'=>'Pharmacist'] as $val => $label)
              <option value="{{ $val }}" {{ $user->role === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
          @error('role')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>

  {{-- Account info & reset password --}}
  <div class="col-stack">

    <div class="card">
      <div class="card-header"><div class="card-title">Account Information</div></div>
      <div class="card-body">
        <div class="detail-row"><span class="k">User ID</span><span class="v">#{{ $user->id }}</span></div>
        <div class="detail-row"><span class="k">Joined</span><span class="v">{{ $user->created_at->format('d M Y, H:i') }}</span></div>
        <div class="detail-row"><span class="k">Last Login</span><span class="v">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Never' }}</span></div>
        @if($user->nin)
        <div class="detail-row"><span class="k">NIN</span><span class="v">{{ $user->nin }}</span></div>
        @endif
        @if($user->patient)
        <div class="detail-row"><span class="k">MedID</span><span class="v monospace">{{ $user->patient->med_id }}</span></div>
        @endif
        @if($user->provider)
        <div class="detail-row"><span class="k">MDCN Folio</span><span class="v">{{ $user->provider->mdcn_folio ?? '—' }}</span></div>
        <div class="detail-row">
          <span class="k">MDCN Verified</span>
          <span class="v">
            @if($user->provider->is_mdcn_verified)
              <span class="pill green">✓ Verified</span>
            @else
              <span class="pill amber">Pending</span>
            @endif
          </span>
        </div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title">Reset Password</div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}"
              data-confirm="Reset password for {{ $user->name }}?"
              data-confirm-btn="btn-secondary"
              data-confirm-ok="Reset Password">
          @csrf
          <div class="form-field mb-12">
            <label>New Password</label>
            <input type="password" name="new_password" required placeholder="Min. 8 characters" autocomplete="new-password">
            @error('new_password')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field mb-16">
            <label>Confirm New Password</label>
            <input type="password" name="new_password_confirmation" required autocomplete="new-password">
          </div>
          <button type="submit" class="btn btn-secondary">🔑 Reset Password</button>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection
