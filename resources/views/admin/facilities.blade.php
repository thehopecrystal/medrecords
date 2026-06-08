@extends('layouts.app')
@section('title', 'Facilities')
@section('topbar-title', 'Facilities')


@section('content')
<div class="page-header"><h1>Facilities</h1><p>All registered facilities on MedRecords</p></div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Name</th><th>Type</th><th>City</th><th>License</th><th>Providers</th><th>Status</th></tr>
      </thead>
      <tbody>
        @forelse($facilities as $f)
          <tr>
            <td><strong>{{ $f->name }}</strong></td>
            <td><span class="pill navy">{{ $f->type }}</span></td>
            <td>{{ $f->city }}, {{ $f->state }}</td>
            <td style="font-family:monospace;font-size:12px;">{{ $f->license_no ?: '—' }}</td>
            <td>{{ $f->providers_count }}</td>
            <td><span class="pill {{ $f->is_active ? 'green' : 'grey' }}">{{ $f->is_active ? 'Active' : 'Inactive' }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--grey-500);padding:24px;">No facilities registered.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $facilities->links() }}</div>
</div>
@endsection
