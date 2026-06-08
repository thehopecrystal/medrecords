@extends('layouts.app')
@section('title', 'Plans & Pricing')
@section('topbar-title', 'Plans & Pricing')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Plans &amp; Pricing</h1>
    <p>Manage subscription plans shown on the public pricing page.</p>
  </div>
  <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">+ New Plan</a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
  @forelse($plans as $plan)
  <div class="card" style="position:relative;overflow:hidden;{{ $plan->is_featured ? 'border:2px solid var(--navy);' : '' }}">
    @if($plan->badge)
      <div style="position:absolute;top:14px;right:-22px;background:var(--navy);color:#fff;font-size:10px;font-weight:700;padding:4px 30px;transform:rotate(35deg);letter-spacing:0.04em;">
        {{ $plan->badge }}
      </div>
    @endif
    <div class="card-pad">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;">
        <div>
          <div style="font-size:16px;font-weight:800;color:var(--navy);">{{ $plan->name }}</div>
          @if(!$plan->is_active)<span class="pill grey" style="font-size:10px;">Inactive</span>@endif
        </div>
        <div style="text-align:right;">
          <div style="font-size:22px;font-weight:800;color:{{ $plan->isFree() ? 'var(--green)' : 'var(--navy)' }};">
            {{ $plan->formattedPrice() }}
          </div>
          <div style="font-size:11px;color:var(--text-3);">Sort: {{ $plan->sort_order }}</div>
        </div>
      </div>
      <p style="font-size:12px;color:var(--text-2);margin-bottom:14px;min-height:36px;">{{ $plan->description }}</p>
      <ul style="list-style:none;margin:0 0 16px;padding:0;font-size:12px;color:var(--text-2);">
        @foreach(array_slice($plan->features ?? [], 0, 5) as $f)
          <li style="padding:3px 0;display:flex;gap:6px;">
            <span style="color:var(--green);">✓</span> {{ $f }}
          </li>
        @endforeach
        @if(count($plan->features ?? []) > 5)
          <li style="color:var(--text-3);padding:3px 0;">+ {{ count($plan->features) - 5 }} more…</li>
        @endif
      </ul>
      <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;">Edit</a>
        <form method="POST" action="{{ route('admin.plans.destroy', $plan->id) }}" style="flex:1;">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm" style="width:100%;"
            onclick="return confirm('Delete plan {{ addslashes($plan->name) }}?')">Delete</button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="card" style="grid-column:1/-1;">
    <div class="empty-state">
      <div class="empty-icon">💳</div>
      <h3>No plans yet</h3>
      <p><a href="{{ route('admin.plans.create') }}" style="color:var(--navy);">Create your first plan</a> to show pricing on the landing page.</p>
    </div>
  </div>
  @endforelse
</div>
@endsection
