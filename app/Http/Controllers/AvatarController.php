<?php

namespace App\Http\Controllers;

use App\Rules\SafeFileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048',
                         'dimensions:max_width=3000,max_height=3000',
                         new SafeFileType(['image/jpeg', 'image/png', 'image/webp'])],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Use a non-guessable name — never expose the original filename.
        $ext  = $request->file('avatar')->getClientOriginalExtension();
        $name = 'avatars/' . $user->id . '_' . Str::uuid() . '.' . strtolower($ext);
        $path = $request->file('avatar')->storeAs('', $name, 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile picture updated successfully.');
    }
}
