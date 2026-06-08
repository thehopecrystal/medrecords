<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Services\MdcnService;

class MdcnController extends Controller
{
    public function providers()
    {
        $providers = Provider::with(['user', 'facility'])
            ->orderBy('mdcn_verified')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('admin.providers', compact('providers'));
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
