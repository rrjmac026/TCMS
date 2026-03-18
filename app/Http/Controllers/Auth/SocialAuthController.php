<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        // Dynamically set the redirect URL to current tenant domain
        $redirectUrl = url('/auth/google/callback');
        

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        $redirectUrl = url('/auth/google/callback');

        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }

        // Find or create user
        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            // Auto-register as trainee
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'password'          => null, // No password for OAuth users
                'role'              => 'trainee',
                'email_verified_at' => now(),
            ]);

            // Notify admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'New Trainee Registration (Google)',
                    'message' => "A new trainee '{$user->name}' registered via Google with email {$user->email}.",
                    'link'    => route('admin.trainees.index'),
                ]);
            }
        } else {
            // Update google_id if not set
            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        Auth::login($user, remember: true);

        return match ($user->role) {
            'admin'   => redirect()->intended(route('admin.dashboard')),
            'trainer' => redirect()->intended(route('trainer.dashboard')),
            'trainee' => redirect()->intended(route('trainee.dashboard')),
            default   => redirect('/'),
        };
    }
}