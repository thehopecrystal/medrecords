<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    private function superAdminOnly(): void
    {
        abort_unless(auth()->user()->role === 'super_admin', 403, 'Super admin access required.');
    }

    public function index()
    {
        $this->superAdminOnly();
        $plans = Plan::orderBy('sort_order')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $this->superAdminOnly();
        $plan = new Plan(['billing_period' => 'monthly', 'is_active' => true, 'sort_order' => Plan::max('sort_order') + 1]);
        return view('admin.plans.form', compact('plan'));
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
        $plan = Plan::findOrFail($id);
        return view('admin.plans.form', compact('plan'));
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
