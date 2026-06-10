<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    private function superAdminOnly(): void
    {
        abort_unless(auth()->user()->role === 'super_admin', 403, 'Super admin access required.');
    }

    public function index(Request $request)
    {
        $this->superAdminOnly();

        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(25)->withQueryString();

        $roleCounts = User::selectRaw('role, count(*) as total')
            ->groupBy('role')->pluck('total', 'role');

        $usersData = $users->through(fn ($u) => [
            'id'                  => $u->id,
            'name'                => $u->name,
            'email'               => $u->email,
            'phone'               => $u->phone,
            'role'                => $u->role,
            'is_active'           => $u->is_active,
            'nin'                 => $u->nin,
            'last_login_human'    => $u->last_login_at?->diffForHumans(),
            'created_at_formatted'=> $u->created_at->format('d M Y'),
        ]);

        return Inertia::render('Admin/Users/Index', [
            'users'      => $usersData,
            'roleCounts' => $roleCounts,
            'filters'    => $request->only(['search', 'role', 'status']),
        ]);
    }

    public function create()
    {
        $this->superAdminOnly();
        $facilities = Facility::orderBy('name')->get(['id', 'name']);
        return Inertia::render('Admin/Users/Create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $this->superAdminOnly();

        $isProvider = in_array($request->role, ['provider', 'pharmacist']);

        $request->validate([
            'name'        => 'required|string|max:120',
            'email'       => 'required|email|unique:users,email',
            'phone'       => ['required', 'regex:/^(\+?[0-9]{10,15})$/', 'unique:users,phone'],
            'role'        => 'required|in:patient,provider,pharmacist,facility_admin,super_admin',
            'password'    => 'required|string|min:8|confirmed',
            'facility_id' => $isProvider ? 'required|exists:facilities,id' : 'nullable',
            'specialty'   => $isProvider ? 'required|string|max:100' : 'nullable|string|max:100',
            'license_no'  => 'nullable|string|max:60',
        ], [
            'phone.regex'        => 'Phone must be a valid number (e.g. 08012345678 or +2348012345678).',
            'facility_id.required' => 'A facility must be assigned for provider and pharmacist accounts.',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $this->normalizePhone($request->phone),
            'role'      => $request->role,
            'is_active' => true,
            'password'  => Hash::make($request->password),
        ]);

        if ($request->role === 'patient') {
            Patient::create([
                'user_id' => $user->id,
                'med_id'  => 'MR-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(6)),
            ]);
        }

        if ($isProvider) {
            Provider::create([
                'user_id'     => $user->id,
                'facility_id' => $request->facility_id,
                'specialty'   => $request->specialty ?? 'General',
                'license_no'  => $request->license_no,
            ]);
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User {$user->name} created successfully.");
    }

    public function show(int $id)
    {
        $this->superAdminOnly();

        $user = User::with(['patient', 'provider.facility'])->findOrFail($id);

        $userData = [
            'id'                   => $user->id,
            'name'                 => $user->name,
            'email'                => $user->email,
            'phone'                => $user->phone,
            'role'                 => $user->role,
            'is_active'            => $user->is_active,
            'nin'                  => $user->nin,
            'created_at_formatted' => $user->created_at->format('d M Y, H:i'),
            'last_login_formatted' => $user->last_login_at?->format('d M Y, H:i'),
            'patient'              => $user->patient ? ['med_id' => $user->patient->med_id] : null,
            'provider'             => $user->provider ? [
                'mdcn_folio'       => $user->provider->mdcn_folio,
                'is_mdcn_verified' => (bool) $user->provider->mdcn_verified,
            ] : null,
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => $userData,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->superAdminOnly();

        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:120',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'regex:/^(\+?[0-9]{10,15})$/', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|in:patient,provider,pharmacist,facility_admin,super_admin',
        ], [
            'phone.regex' => 'Phone must be a valid number (e.g. 08012345678 or +2348012345678).',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $this->normalizePhone($request->phone),
            'role'  => $request->role,
        ]);

        return back()->with('success', 'User updated successfully.');
    }

    public function toggleActive(int $id)
    {
        $this->superAdminOnly();

        $user = User::findOrFail($id);

        // Prevent deactivating yourself
        abort_if($user->id === auth()->id(), 422, 'You cannot deactivate your own account.');

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$user->name} has been {$status}.");
    }

    public function resetPassword(Request $request, int $id)
    {
        $this->superAdminOnly();

        $request->validate(['new_password' => 'required|string|min:8|confirmed']);

        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', "Password reset for {$user->name}.");
    }

    public function destroy(int $id)
    {
        $this->superAdminOnly();

        $user = User::findOrFail($id);
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own account.');

        $user->delete();

        return redirect()->route('admin.users')->with('success', "{$user->name} has been deleted.");
    }

    /** Normalise Nigerian phone numbers to E.164 (+234XXXXXXXXX). */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '234')) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+234' . substr($digits, 1);
        }

        return '+' . $digits;
    }
}
