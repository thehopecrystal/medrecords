<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Provider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    private function myFacility(): Facility
    {
        return Facility::where('owner_user_id', auth()->id())->firstOrFail();
    }

    public function index(): JsonResponse
    {
        // Super admin sees all facilities; facility_admin sees only their facility.
        if (auth()->user()->role === 'super_admin') {
            $facilities = Facility::with(['providers.user', 'owner'])->orderBy('name')->get();
            return response()->json(['facilities' => $facilities]);
        }

        $facility  = $this->myFacility();
        $team      = Provider::where('facility_id', $facility->id)->with('user')->get();
        $available = Provider::whereNull('facility_id')->with('user')->get();

        return response()->json(['facility' => $facility, 'team' => $team, 'available' => $available]);
    }

    public function add(Request $request): JsonResponse
    {
        $facility = auth()->user()->role === 'super_admin'
            ? Facility::findOrFail($request->input('facility_id'))
            : $this->myFacility();

        $request->validate(['provider_id' => 'required|exists:providers,id']);

        $provider = Provider::findOrFail($request->provider_id);

        if ($provider->facility_id !== null && $provider->facility_id !== $facility->id) {
            return response()->json(['message' => 'This provider is already assigned to another facility.'], 422);
        }

        $provider->update(['facility_id' => $facility->id]);

        return response()->json(['message' => $provider->user->name . ' has been added to ' . $facility->name . '.', 'provider' => $provider->load('user')]);
    }

    public function remove(int $providerId): JsonResponse
    {
        $provider = auth()->user()->role === 'super_admin'
            ? Provider::findOrFail($providerId)
            : Provider::where('id', $providerId)->where('facility_id', $this->myFacility()->id)->firstOrFail();

        $name = $provider->user->name;
        $provider->update(['facility_id' => null]);

        return response()->json(['message' => $name . ' has been removed from the facility.']);
    }
}
