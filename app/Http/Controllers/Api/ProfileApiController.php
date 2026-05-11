<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return response()->json([
            'data' => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => $user->role,
                'email_verified' => $user->hasVerifiedEmail(),
                'two_factor'     => $user->two_factor_enabled,
                'orders_count'   => $user->orders()->count(),
                'reviews_count'  => $user->reviews()->count(),
                'member_since'   => $user->created_at?->toDateString(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'data'    => $user->fresh(),
            'message' => 'Profile updated',
        ]);
    }
}
