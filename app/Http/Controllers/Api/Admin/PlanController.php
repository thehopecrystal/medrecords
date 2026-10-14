<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    private function superAdminOnly(): void
    {
        abort_unless(auth()->user()->role === 'super_admin', 403, 'Super admin access required.');
    }

    public function index(): JsonResponse
    {
        $this->superAdminOnly();
        $plans = Plan::orderBy('sort_order')->get();
        return response()->json(['plans' => $plans]);
    }

    /** Create-form defaults. Accepts `features` as an array instead of raw text. */
    public function create(): JsonResponse
    {
        $this->superAdminOnly();
        $plan = new Plan(['billing_period' => 'monthly', 'is_active' => true, 'sort_order' => Plan::max('sort_order') + 1]);
        return response()->json([
            'defaults' => $plan->only(['billing_period', 'is_active', 'sort_order']),
            'fields'   => ['name' => 'required|string|max:80', 'price' => 'required|numeric|min:0', 'billing_period' => 'required|in:free,monthly,yearly,one-time'],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->superAdminOnly();

        $data = $this->validated($request);
        $data['features'] = is_array($request->input('features'))
            ? array_values(array_filter($request->input('features')))
            : $this->parseFeatures($request->features_raw ?? '');
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $plan = Plan::create($data);

        return response()->json(['message' => 'Plan created successfully.', 'plan' => $plan], 201);
    }

    public function edit(int $id): JsonResponse
    {
        $this->superAdminOnly();
        $plan = Plan::findOrFail($id);
        return response()->json(['plan' => $plan]);
    }

    public function show(int $id): JsonResponse
    {
        $this->superAdminOnly();
        return response()->json(['plan' => Plan::findOrFail($id)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->superAdminOnly();

        $plan = Plan::findOrFail($id);
        $data = $this->validated($request, $id);
        $data['features'] = is_array($request->input('features'))
            ? array_values(array_filter($request->input('features')))
            : $this->parseFeatures($request->features_raw ?? '');

        $plan->update($data);

        return response()->json(['message' => 'Plan updated successfully.', 'plan' => $plan]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->superAdminOnly();
        Plan::findOrFail($id)->delete();
        return response()->json(['message' => 'Plan deleted.']);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'           => 'required|string|max:80',
            'slug'           => 'nullable|string|max:80|unique:plans,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
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
