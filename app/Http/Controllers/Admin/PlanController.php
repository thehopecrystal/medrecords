<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PlanController extends Controller
{
    private function superAdminOnly(): void
    {
        abort_unless(auth()->user()->role === 'super_admin', 403, 'Super admin access required.');
    }

    public function index()
    {
        $this->superAdminOnly();
        $plans = Plan::orderBy('sort_order')->get()->map(fn ($p) => [
            'id'             => $p->id,
            'name'           => $p->name,
            'slug'           => $p->slug,
            'description'    => $p->description,
            'price'          => (float) $p->price,
            'billing_period' => $p->billing_period,
            'badge'          => $p->badge,
            'sort_order'     => $p->sort_order,
            'is_active'      => (bool) $p->is_active,
            'is_featured'    => (bool) $p->is_featured,
            'features'       => $p->features ?? [],
            'formatted_price'=> $p->formattedPrice(),
        ]);

        return Inertia::render('Admin/Plans/Index', compact('plans'));
    }

    public function create()
    {
        $this->superAdminOnly();
        $plan = [
            'id'             => null,
            'name'           => '',
            'slug'           => '',
            'description'    => '',
            'price'          => 0,
            'billing_period' => 'monthly',
            'badge'          => '',
            'sort_order'     => (int) Plan::max('sort_order') + 1,
            'is_active'      => true,
            'is_featured'    => false,
            'features'       => [],
        ];

        return Inertia::render('Admin/Plans/Form', compact('plan'));
    }

    public function store(Request $request)
    {
        $this->superAdminOnly();

        $data = $this->validated($request);
        $data['features'] = $this->parseFeatures($request->features_raw ?? '');
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Plan::create($data);

        return redirect()->route('admin.plans')->with('success', 'Plan created successfully.');
    }

    public function edit(int $id)
    {
        $this->superAdminOnly();
        $p = Plan::findOrFail($id);
        $plan = [
            'id'             => $p->id,
            'name'           => $p->name,
            'slug'           => $p->slug,
            'description'    => $p->description,
            'price'          => (float) $p->price,
            'billing_period' => $p->billing_period,
            'badge'          => $p->badge,
            'sort_order'     => $p->sort_order,
            'is_active'      => (bool) $p->is_active,
            'is_featured'    => (bool) $p->is_featured,
            'features'       => $p->features ?? [],
        ];

        return Inertia::render('Admin/Plans/Form', compact('plan'));
    }

    public function update(Request $request, int $id)
    {
        $this->superAdminOnly();

        $plan = Plan::findOrFail($id);
        $data = $this->validated($request, $id);
        $data['features'] = $this->parseFeatures($request->features_raw ?? '');

        $plan->update($data);

        return redirect()->route('admin.plans')->with('success', 'Plan updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->superAdminOnly();
        Plan::findOrFail($id)->delete();
        return back()->with('success', 'Plan deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'           => 'required|string|max:80',
            'slug'           => "nullable|string|max:80|unique:plans,slug" . ($ignoreId ? ",{$ignoreId}" : ''),
            'description'    => 'nullable|string|max:500',
            'price'          => 'required|numeric|min:0',
            'billing_period' => 'required|in:free,monthly,yearly,one-time',
            'badge'          => 'nullable|string|max:40',
            'sort_order'     => 'required|integer|min:0',
            'is_active'      => 'boolean',
            'is_featured'    => 'boolean',
        ]);
    }

    private function parseFeatures(string $raw): array
    {
        return array_values(array_filter(
            array_map('trim', explode("\n", $raw))
        ));
    }
}
