<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    private function myFacility(): Facility
    {
        return Facility::where('owner_user_id', auth()->id())->firstOrFail();
    }

    public function index()
    {
        // Super admin sees a facility picker; facility_admin sees only their facility.
        if (auth()->user()->role === 'super_admin') {
            $facilities = Facility::with(['providers.user', 'owner'])->orderBy('name')->get();
            return view('admin.team', compact('facilities'));
        }

        $facility  = $this->myFacility();
        $team      = Provider::where('facility_id', $facility->id)->with('user')->get();
        $available = Provider::whereNull('facility_id')->with('user')->get();
        $facilities = null;

        return view('admin.team', compact('facility', 'team', 'available', 'facilities'));
    }

    public function add(Request $request)
    {
        $facility = auth()->user()->role === 'super_admin'
            ? Facility::findOrFail($request->input('facility_id'))
            : $this->myFacility();

        $request->validate(['provider_id' => 'required|exists:providers,id']);

        $provider = Provider::findOrFail($request->provider_id);
        $provider->update(['facility_id' => $facility->id]);

        return back()->with('success', $provider->user->name . ' has been added to ' . $facility->name . '.');
    }

    public function remove(int $providerId)
    {
        $provider = auth()->user()->role === 'super_admin'
            ? Provider::findOrFail($providerId)
            : Provider::where('id', $providerId)->where('facility_id', $this->myFacility()->id)->firstOrFail();

        $name = $provider->user->name;
        $provider->update(['facility_id' => null]);

        return back()->with('success', $name . ' has been removed from the facility.');
    }
}
