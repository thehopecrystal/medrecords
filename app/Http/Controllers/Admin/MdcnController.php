<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Services\MdcnService;
use Inertia\Inertia;

class MdcnController extends Controller
{
    public function providers()
    {
        $providers = Provider::with(['user', 'facility'])
            ->orderBy('mdcn_verified')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $providersData = $providers->through(fn ($p) => [
            'id'                => $p->id,
            'user_name'         => $p->user?->name,
            'user_email'        => $p->user?->email,
            'facility_name'     => $p->facility?->name,
            'specialty'         => $p->specialty,
            'mdcn_folio'        => $p->mdcn_folio,
            'mdcn_verified'     => (bool) $p->mdcn_verified,
            'mdcn_verified_at'  => $p->mdcn_verified_at?->format('d M Y'),
            'mdcn_status'       => $p->mdcn_status,
        ]);

        return Inertia::render('Admin/Providers', [
            'providers' => $providersData,
        ]);
    }

    public function verify(int $id)
    {
        $provider = Provider::with('user')->findOrFail($id);
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
