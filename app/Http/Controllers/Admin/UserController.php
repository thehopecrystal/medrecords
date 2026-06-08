<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    public function create()
    {
        $this->superAdminOnly();
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->superAdminOnly();

        $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email|unique:users,email',
            'phone'    => ['required', 'regex:/^0[789][01]\d{8}$/', 'unique:users,phone'],
            'role'     => 'required|in:patient,provider,pharmacist,facility_admin,super_admin',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'role'      => $request->role,
            'is_active' => true,
            'password'  => Hash::make($request->password),
        ]);

        // Auto-create supporting records
        if ($request->role === 'patient') {
            Patient::create([
                'user_id' => $user->id,
                'med_id'  => 'MR-' . strtoupper(Str::random(4)) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999),
            ]);
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User {$user->name} created successfully.");
    }

    public function show(int $id)
    {
        $this->superAdminOnly();

        $user = User::with(['patient', 'provider.facility'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        $this->superAdminOnly();

        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:120',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'regex:/^0[789][01]\d{8}$/', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|in:patient,provider,pharmacist,facility_admin,super_admin',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
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
}
