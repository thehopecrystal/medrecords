<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request): JsonResponse
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
        $roleCounts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return response()->json(['users' => $users, 'role_counts' => $roleCounts]);
    }

    /** Facility options for the create-user form. */
    public function create(): JsonResponse
    {
        $this->superAdminOnly();
        $facilities = Facility::orderBy('name')->get(['id', 'name']);
        return response()->json(['facilities' => $facilities]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->superAdminOnly();

        $isProvider = in_array($request->role, ['provider', 'pharmacist']);

        $request->validate([
            'name'        => 'required|string|max:120',
            'email'       => 'required|email|unique:users,email',
            'phone'       => ['required', 'regex:/^(\+?[0-9]{10,15})$/', 'unique:users,phone'],
            'role'        => 'required|in:patient,provider,pharmacist',
            'password'    => 'required|string|min:8|confirmed',
            'facility_id' => $isProvider ? 'required|exists:facilities,id' : 'nullable',
            'specialty'   => $isProvider ? 'required|string|max:100' : 'nullable|string|max:100',
            'license_no'  => 'nullable|string|max:60',
        ], [
            'phone.regex'        => 'Phone must be a valid number (e.g. 08012345678 or +2348012345678).',
            'facility_id.required' => 'A facility must be assigned for provider and pharmacist accounts.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $this->normalizePhone($request->phone),
            'password' => Hash::make($request->password),
        ]);
        $user->role      = $request->role;
        $user->is_active = true;
        $user->save();

        if ($request->role === 'patient') {
            $patient = new Patient([
                'med_id' => 'MR-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(6)),
            ]);
            $patient->user_id = $user->id;
            $patient->save();
        }

        if ($isProvider) {
            Provider::create([
                'user_id'     => $user->id,
                'facility_id' => $request->facility_id,
                'specialty'   => $request->specialty ?? 'General',
                'license_no'  => $request->license_no,
            ]);
        }

        return response()->json([
            'message' => "User {$user->name} created successfully.",
            'user'    => $user->load(['patient', 'provider.facility']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $this->superAdminOnly();
        $user = User::with(['patient', 'provider.facility'])->findOrFail($id);

        return response()->json(['user' => $user]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->superAdminOnly();
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:120',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'regex:/^(\+?[0-9]{10,15})$/', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|in:patient,provider,pharmacist',
        ], [
            'phone.regex' => 'Phone must be a valid number (e.g. 08012345678 or +2348012345678).',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $this->normalizePhone($request->phone),
        ]);
        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'User updated successfully.', 'user' => $user]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $this->superAdminOnly();
        $user = User::findOrFail($id);

        // Prevent deactivating yourself (ids live in separate tables for admins)
        abort_if($user->id === auth()->id(), 422, 'You cannot deactivate your own account.');

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return response()->json(['message' => "{$user->name} has been {$status}.", 'user' => $user]);
    }

    public function resetPassword(Request $request, int $id): JsonResponse
    {
        $this->superAdminOnly();
        $request->validate(['new_password' => 'required|string|min:8|confirmed']);

        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => "Password reset for {$user->name}."]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->superAdminOnly();
        $user = User::findOrFail($id);
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own account.');

        $user->delete();

        return response()->json(['message' => "{$user->name} has been deleted."]);
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
