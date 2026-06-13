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

<div class="plan-auto-grid">
  @forelse($plans as $plan)
  <div class="card plan-card{{ $plan->is_featured ? ' plan-featured' : '' }}">
    <div class="card-pad">
      <div class="plan-header-row">
        <div>
          <div class="plan-name">{{ $plan->name }}</div>
          <div class="plan-badge-row">
            @if(!$plan->is_active)
              <span class="pill grey pill-xs">Inactive</span>
            @endif
            @if($plan->badge)
              <span class="pill navy pill-xs">{{ $plan->badge }}</span>
            @endif
          </div>
        </div>
        <div class="plan-price-col">
          <div class="plan-price {{ $plan->isFree() ? 'plan-price-green' : 'plan-price-navy' }}">
            {{ $plan->formattedPrice() }}
          </div>
          <div class="plan-order">order: {{ $plan->sort_order }}</div>
        </div>
      </div>

      <p class="plan-desc">{{ $plan->description }}</p>

      <ul class="plan-features">
        @foreach(array_slice($plan->features ?? [], 0, 5) as $f)
          <li class="plan-feature-item">
            <span class="plan-check">✓</span> {{ $f }}
          </li>
        @endforeach
        @if(count($plan->features ?? []) > 5)
          <li class="plan-more">+ {{ count($plan->features) - 5 }} more features…</li>
        @endif
      </ul>

      <div class="plan-actions">
        <a href="{{ route('admin.plans.edit', $plan->id) }}"
           class="btn btn-secondary btn-sm btn-stretch">Edit</a>
        <form method="POST" action="{{ route('admin.plans.destroy', $plan->id) }}" class="btn-stretch"
              data-confirm="Delete plan {{ $plan->name }}? This cannot be undone."
              data-confirm-title="Delete Plan"
              data-confirm-ok="Delete">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm btn-full">Delete</button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="card card-span-full">
    <div class="empty-state">
      <div class="empty-icon">💳</div>
      <h3>No plans yet</h3>
      <p>
        <a href="{{ route('admin.plans.create') }}" class="text-navy-link">Create your first plan</a>
        to display pricing on the landing page.
      </p>
    </div>
  </div>
  @endforelse
</div>

@endsection
