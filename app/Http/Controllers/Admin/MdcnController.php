<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Provider;
use App\Services\MdcnService;

class MdcnController extends Controller
{
    public function providers()
    {
        $query = Provider::with(['user', 'facility'])
            ->orderBy('mdcn_verified')
            ->orderBy('created_at', 'desc');

        if ($search = request('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('mdcn_folio', 'like', "%{$search}%");
        }

        if (request('status') === 'verified') {
            $query->where('mdcn_verified', true);
        } elseif (request('status') === 'unverified') {
            $query->where('mdcn_verified', false);
        }

        $providers = $query->paginate(30)->withQueryString();
        $counts = [
            'all'        => Provider::count(),
            'verified'   => Provider::where('mdcn_verified', true)->count(),
            'unverified' => Provider::where('mdcn_verified', false)->count(),
        ];

        return view('admin.providers', compact('providers', 'counts'));
    }

    public function verify(int $id)
    {
        $provider = Provider::with('user')->findOrFail($id);

        // Facility admins may only trigger MDCN verification for providers at their own facility.
        /** @var Admin $admin */
        $admin = auth()->user();
        if ($admin->role === 'facility_admin') {
            abort_unless(
                $provider->facility_id === $admin->facility_id,
                403,
                'You may only verify providers at your facility.'
            );
        }

        $result   = (new MdcnService())->verify($provider->mdcn_folio ?? '');

        $provider->update([
            'mdcn_verified'    => $result['verified'],
            'mdcn_verified_at' => $result['verified'] ? now() : null,
            'mdcn_status'      => $result['status'],
        ]);

        $msg = $result['verified']
            ? "MDCN verification confirmed for {$provider->user->name}."
            : "MDCN verification pending for {$provider->user->name}: {$result['message']}";

        return back()->with($result['verified'] ? 'success' : 'error', $msg);
    }
}
