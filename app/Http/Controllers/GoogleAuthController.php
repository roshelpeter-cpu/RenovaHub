<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google so Socialite can handle the OAuth process.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Sign in an existing RenovaHub account. A Google email that is not
     * already registered must choose a role on the registration form,
     * so this callback never creates a user.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->with('status', 'Google sign-in could not be completed. Please try again.');
        }

        $email = $googleUser->getEmail();

        $user = is_string($email) && $email !== ''
            ? User::query()->where('email', $email)->first()
            : null;

        if (! $user) {
            return redirect()
                ->route('register')
                ->with('status', 'No RenovaHub account was found for this Google account. Please register first.');
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
