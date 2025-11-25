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
        // Handle profile picture upload if present (file or base64-cropped)
    if($request->filled('profile_picture_cropped')){
            // base64 like: data:image/jpeg;base64,....
            $b64 = $request->input('profile_picture_cropped');
            if(str_starts_with($b64, 'data:image')){
                [$meta,$content] = explode(',', $b64, 2);
                $bin = base64_decode($content);
        $ext = str_contains($meta,'png') ? 'png' : 'jpg';
        $dir = \App\Support\StoragePath::userAvatarDir($request->user()->id);
        $name = \Illuminate\Support\Str::ulid()->toBase32().'.'.$ext;
        $path = $dir.'/'.$name;
        \Storage::put($path, $bin);
        $data['profile_picture_path'] = $path;
            }
        } elseif($request->hasFile('profile_picture')){
            $file = $request->file('profile_picture');
            $dir = \App\Support\StoragePath::userAvatarDir($request->user()->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $path = \Storage::putFileAs($dir, $file, $name);
            $data['profile_picture_path'] = $path;
        }
        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

    $request->user()->save();

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
