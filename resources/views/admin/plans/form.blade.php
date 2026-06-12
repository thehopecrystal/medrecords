@extends('layouts.app')
@section('title', isset($plan->id) ? 'Edit Plan' : 'Create Plan')
@section('topbar-title', isset($plan->id) ? 'Edit Plan' : 'Create Plan')

@section('content')
<div class="page-header-row">
  <div>
    <h1>{{ isset($plan->id) ? 'Edit Plan: '.$plan->name : 'Create New Plan' }}</h1>
    <p>{{ isset($plan->id) ? 'Update the plan details below.' : 'Define a new subscription tier.' }}</p>
  </div>
  <a href="{{ route('admin.plans') }}" class="btn btn-secondary">← Back to Plans</a>
</div>

<div class="max-720">
  <form method="POST" action="{{ isset($plan->id) ? route('admin.plans.update', $plan->id) : route('admin.plans.store') }}">
    @csrf
    @if(isset($plan->id)) @method('PUT') @endif

    <div class="card mb-20">
      <div class="card-header"><div class="card-title">Plan Details</div></div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field">
            <label>Plan Name *</label>
            <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" required placeholder="e.g. Basic">
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $plan->slug ?? '') }}" placeholder="auto-generated from name">
            <div class="form-hint">Leave blank to auto-generate.</div>
            @error('slug')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field span-2">
            <label>Description</label>
            <textarea name="description" rows="2" placeholder="Short plan description shown to users…">{{ old('description', $plan->description ?? '') }}</textarea>
            @error('description')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Price (₦) *</label>
            <input type="number" name="price" value="{{ old('price', $plan->price ?? 0) }}" min="0" step="0.01" required>
            @error('price')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Billing Period *</label>
            <select name="billing_period" required>
              @foreach(['free'=>'Free (₦0)','monthly'=>'Monthly','yearly'=>'Yearly','one-time'=>'One-time / Contact'] as $val => $label)
                <option value="{{ $val }}" {{ old('billing_period', $plan->billing_period ?? 'monthly') === $val ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @error('billing_period')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Badge Label</label>
            <input type="text" name="badge" value="{{ old('badge', $plan->badge ?? '') }}" placeholder="e.g. Most Popular, Contact us">
            <div class="form-hint">Shown as a ribbon on the pricing card.</div>
          </div>
          <div class="form-field">
            <label>Sort Order *</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order ?? 0) }}" min="0" required>
            <div class="form-hint">Lower numbers appear first.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-20">
      <div class="card-header"><div class="card-title">Features List</div></div>
      <div class="card-body">
        <div class="form-field">
          <label>Features (one per line)</label>
          <textarea name="features_raw" rows="10" placeholder="Unlimited health records&#10;Access code generation&#10;5 dependent records&#10;…">{{ old('features_raw', implode("\n", $plan->features ?? [])) }}</textarea>
          <div class="form-hint">Each line becomes one feature bullet. Empty lines are ignored.</div>
        </div>
      </div>
    </div>

    <div class="card mb-24">
      <div class="card-header"><div class="card-title">Visibility</div></div>
      <div class="card-body">
        <div class="check-row">
          <label class="check-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }} class="check-input">
            Active (show on pricing page)
          </label>
          <label class="check-label">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $plan->is_featured ?? false) ? 'checked' : '' }} class="check-input">
            Featured (highlighted card)
          </label>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">{{ isset($plan->id) ? 'Save Changes' : 'Create Plan' }}</button>
      <a href="{{ route('admin.plans') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
