<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Remove image from data array as it's handled separately
        $hasImage = isset($data['image']);
        unset($data['image']);

        $user = $request->user();
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Handle image upload
        if ($hasImage && $request->hasFile('image')) {
            try {
                // Clear existing media first (since it's singleFile collection)
                $user->clearMediaCollection('avatar');
                $user->addMediaFromRequest('image')
                    ->usingName($user->name ?? 'avatar')
                    ->usingFileName($request->file('image')->getClientOriginalName())
                    ->toMediaCollection('avatar');
            } catch (\Exception $e) {
                // Log error and redirect back with error message
                \Log::error('Failed to upload profile image: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                return Redirect::route('profile.edit')
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
