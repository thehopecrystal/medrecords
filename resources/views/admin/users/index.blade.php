@extends('layouts.app')
@section('title', 'Users & Roles')
@section('topbar-title', 'Users & Roles')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Users &amp; Roles</h1>
    <p>Manage all platform accounts — patients, providers and pharmacists.</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ New User</a>
</div>

{{-- Stats row: only roles that live in the users table --}}
<div class="stat-grid" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-label">Patients</div>
    <div class="stat-value">{{ $roleCounts['patient'] ?? 0 }}</div>
    <div class="stat-icon">👤</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Providers</div>
    <div class="stat-value">{{ $roleCounts['provider'] ?? 0 }}</div>
    <div class="stat-icon">🩺</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Pharmacists</div>
    <div class="stat-value">{{ $roleCounts['pharmacist'] ?? 0 }}</div>
    <div class="stat-icon">💊</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total</div>
    <div class="stat-value">{{ ($roleCounts['patient'] ?? 0) + ($roleCounts['provider'] ?? 0) + ($roleCounts['pharmacist'] ?? 0) }}</div>
    <div class="stat-sub">All accounts</div>
    <div class="stat-icon">🌐</div>
  </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-pad">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
      <div class="form-field" style="flex:1;min-width:200px;">
        <label>Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, or phone…">
      </div>
      <div class="form-field" style="width:160px;">
        <label>Role</label>
        <select name="role">
          <option value="all">All roles</option>
          <option value="patient"    {{ request('role') === 'patient'    ? 'selected' : '' }}>Patient</option>
          <option value="provider"   {{ request('role') === 'provider'   ? 'selected' : '' }}>Provider</option>
          <option value="pharmacist" {{ request('role') === 'pharmacist' ? 'selected' : '' }}>Pharmacist</option>
        </select>
      </div>
      <div class="form-field" style="width:140px;">
        <label>Status</label>
        <select name="status">
          <option value="">All</option>
          <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm" style="align-self:flex-end;">Filter</button>
      @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm" style="align-self:flex-end;">Clear</a>
      @endif
    </form>
  </div>
</div>

{{-- Table --}}
<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email / Phone</th>
          <th>Role</th>
          <th>Status</th>
          <th>Last Login</th>
          <th>Joined</th>
          <th style="width:80px;"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        @php
          $roleColor = ['patient'=>'navy','provider'=>'green','pharmacist'=>'green'][$user->role] ?? 'grey';
          $roleLabel = ['patient'=>'Patient','provider'=>'Provider','pharmacist'=>'Pharmacist'][$user->role] ?? $user->role;
        @endphp
        <tr>
          <td style="color:var(--text-3);font-size:12px;">{{ $user->id }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--navy));color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
              <div>
                <div style="font-weight:600;font-size:13px;">{{ $user->name }}</div>
                @if($user->nin)
                <div style="font-size:11px;color:var(--text-3);">NIN: {{ $user->nin }}</div>
                @endif
              </div>
            </div>
          </td>
          <td>
            <div style="font-size:13px;">{{ $user->email }}</div>
            <div style="font-size:11px;color:var(--text-3);">{{ $user->phone }}</div>
          </td>
          <td><span class="pill {{ $roleColor }}">{{ $roleLabel }}</span></td>
          <td>
            @if($user->is_active)
              <span class="pill green">● Active</span>
            @else
              <span class="pill red">● Inactive</span>
            @endif
          </td>
          <td style="font-size:12px;color:var(--text-2);">
            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
          </td>
          <td style="font-size:12px;color:var(--text-2);">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-ghost btn-sm">View</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div class="empty-state">
              <div class="empty-icon">👥</div>
              <h3>No users found</h3>
              <p>
                @if(request()->hasAny(['search','role','status']))
                  Try adjusting your filters or <a href="{{ route('admin.users') }}" style="color:var(--navy);">clear them</a>.
                @else
                  <a href="{{ route('admin.users.create') }}" style="color:var(--navy);">Create the first user</a> to get started.
                @endif
              </p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
  <div style="padding:14px 22px;border-top:1px solid var(--border);">
    {{ $users->links() }}
  </div>
  @endif
</div>
@endsection
