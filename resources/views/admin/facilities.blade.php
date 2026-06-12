@extends('layouts.app')
@section('title', 'Facilities')
@section('topbar-title', 'Facilities')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Facilities</h1>
    <p>All registered healthcare facilities on the platform.</p>
  </div>
  @if(auth()->user()?->role === 'super_admin')
  <a href="{{ route('admin.facilities.import') }}" class="btn btn-primary">📥 Import CSV</a>
  @endif
</div>

{{-- Filters --}}
<div class="card mb-20">
  <div class="card-pad">
    <form method="GET" class="filter-row">
      <div class="form-field filter-field-flex">
        <label>Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, city, state, or licence…">
      </div>
      <div class="form-field filter-field">
        <label>Type</label>
        <select name="type">
          <option value="">All types</option>
          @foreach(['hospital','clinic','pharmacy','laboratory','diagnostic'] as $t)
          <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
          @endforeach
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
      @if(request()->hasAny(['search','type','status']))
        <a href="{{ route('admin.facilities') }}" class="btn btn-secondary btn-sm self-end">Clear</a>
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
          <th>Name</th>
          <th>Type</th>
          <th>Location</th>
          <th>Licence No.</th>
          <th>Providers</th>
          <th>Owner</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($facilities as $f)
        <tr>
          <td>
            <div class="td-fw">{{ $f->name }}</div>
            @if($f->email)
            <div class="td-xs-muted">{{ $f->email }}</div>
            @endif
          </td>
          <td><span class="pill navy">{{ ucfirst($f->type ?? 'N/A') }}</span></td>
          <td class="td-sm">{{ implode(', ', array_filter([$f->city, $f->state])) ?: '—' }}</td>
          <td class="td-mono">{{ $f->license_no ?: '—' }}</td>
          <td><span class="td-fw">{{ $f->providers_count }}</span></td>
          <td class="td-muted">{{ $f->owner?->name ?? '—' }}</td>
          <td>
            <span class="pill {{ $f->is_active ? 'green' : 'grey' }}">
              {{ $f->is_active ? '● Active' : '● Inactive' }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <div class="empty-icon">🏥</div>
              <h3>No facilities found</h3>
              <p>
                @if(request()->hasAny(['search','type','status']))
                  Try adjusting your filters.
                @else
                  No facilities have been registered yet.
                @endif
              </p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($facilities->hasPages())
  <div class="pager-row">{{ $facilities->links() }}</div>
  @endif
</div>
@endsection
