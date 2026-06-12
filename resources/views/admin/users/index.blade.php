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

{{-- Stats row --}}
<div class="stat-grid mb-24">
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
<div class="card mb-20">
  <div class="card-pad">
    <form method="GET" class="filter-row">
      <div class="form-field filter-field-flex">
        <label>Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, or phone…">
      </div>
      <div class="form-field filter-field">
        <label>Role</label>
        <select name="role">
          <option value="all">All roles</option>
          <option value="patient"    {{ request('role') === 'patient'    ? 'selected' : '' }}>Patient</option>
          <option value="provider"   {{ request('role') === 'provider'   ? 'selected' : '' }}>Provider</option>
          <option value="pharmacist" {{ request('role') === 'pharmacist' ? 'selected' : '' }}>Pharmacist</option>
        </select>
      </div>
      <div class="form-field filter-field-sm">
        <label>Status</label>
        <select name="status">
          <option value="">All</option>
          <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm self-end">Filter</button>
      @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm self-end">Clear</a>
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
          <th class="th-80"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        @php
          $roleColor = ['patient'=>'navy','provider'=>'green','pharmacist'=>'green'][$user->role] ?? 'grey';
          $roleLabel = ['patient'=>'Patient','provider'=>'Provider','pharmacist'=>'Pharmacist'][$user->role] ?? $user->role;
        @endphp
        <tr>
          <td class="td-id">{{ $user->id }}</td>
          <td>
            <div class="person-cell">
              <div class="avatar avatar-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
              <div>
                <div class="person-name">{{ $user->name }}</div>
                @if($user->nin)
                <div class="person-sub">NIN: {{ $user->nin }}</div>
                @endif
              </div>
            </div>
          </td>
          <td>
            <div class="td-sm">{{ $user->email }}</div>
            <div class="td-xs-muted">{{ $user->phone }}</div>
          </td>
          <td><span class="pill {{ $roleColor }}">{{ $roleLabel }}</span></td>
          <td>
            @if($user->is_active)
              <span class="pill green">● Active</span>
            @else
              <span class="pill red">● Inactive</span>
            @endif
          </td>
          <td class="td-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
          <td class="td-muted">{{ $user->created_at->format('d M Y') }}</td>
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
                  Try adjusting your filters or <a href="{{ route('admin.users') }}" class="text-navy-link">clear them</a>.
                @else
                  <a href="{{ route('admin.users.create') }}" class="text-navy-link">Create the first user</a> to get started.
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
  <div class="pager-row">{{ $users->links() }}</div>
  @endif
</div>
@endsection
