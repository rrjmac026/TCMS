<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * The ONE URI registered in Google Console:
     *   http://tcm.com/auth/google/callback
     *
     * Never changes, no matter how many tenants you add.
     */
    private function centralCallbackUrl(): string
    {
        return config('services.google.redirect');
    }

    // ─────────────────────────────────────────────────────────────────────
    // STEP 1 — Tenant side: acme.tcm.com/auth/google
    //
    // Encode the tenant subdomain in the OAuth `with()` state so we can
    // recover it after Google bounces back to the central domain.
    // ─────────────────────────────────────────────────────────────────────

    public function redirectToGoogle()
    {
        $subdomain = trim(tenancy()->tenant?->subdomain);

        return Socialite::driver('google')
            ->redirectUrl($this->centralCallbackUrl())
            ->with(['state' => $subdomain])
            ->redirect();
    }

    // ─────────────────────────────────────────────────────────────────────
    // STEP 2 — Central side: tcm.com/auth/google/callback
    //
    // Registered in Google Console. Reads the state (subdomain), finds or
    // creates the user in the TENANT database, stores a short-lived token
    // in cache, then bounces the browser back to the tenant subdomain.
    //
    // This method is called from a CENTRAL route (routes/web.php),
    // so tenancy is NOT initialised here — we must boot it manually.
    // ─────────────────────────────────────────────────────────────────────

    public function handleGoogleCallback()
    {
        // ── 1. Exchange code with Google ──────────────────────────────────
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->centralCallbackUrl())
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            return redirect('http://tcm.com:8000/login')
                ->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }

        // ── 2. Recover the tenant subdomain from state ────────────────────
        $subdomain = trim(request('state'));

        if (! $subdomain) {
            return redirect('http://tcm.com:8000/login')
                ->withErrors(['email' => 'Could not determine your organisation. Please try again.']);
        }

        // ── 3. Find the tenant and boot its database ──────────────────────
        $tenant = \App\Models\Tenant::where('subdomain', $subdomain)
            ->where('status', 'approved')
            ->first();

        if (! $tenant) {
            return redirect('http://tcm.com:8000/login')
                ->withErrors(['email' => 'Organisation not found or not yet approved.']);
        }

        // ── 4. Find or create the user INSIDE the tenant database ─────────
        $user = null;

        $tenant->run(function () use ($googleUser, $tenant, &$user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'password'          => null,
                    'role'              => 'trainee',
                    'email_verified_at' => now(),
                ]);

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
                if (! $user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }
        });

        // ── 5. Generate a short-lived one-time token ──────────────────────
        $token = Str::random(64);

        Cache::store('file')->put("google_oauth_{$token}", [
            'user_id'   => $user->id,
            'user_role' => $user->role,
            'subdomain' => $subdomain,
        ], now()->addMinutes(2));

        // ── 6. Bounce back to the tenant subdomain ────────────────────────
        $tenantUrl = "http://{$subdomain}.tcm.com:8000/auth/google/finish?token={$token}";

        return redirect($tenantUrl);
    }

    // ─────────────────────────────────────────────────────────────────────
    // STEP 3 — Tenant side: acme.tcm.com/auth/google/finish
    //
    // Consumes the one-time cache token, logs the user into the tenant
    // session, and redirects to their dashboard.
    // ─────────────────────────────────────────────────────────────────────

    public function finishGoogleLogin()
    {
        $token = request('token');

        if (! $token) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid or missing login token.']);
        }

        // ── Change this line ──
        $data = Cache::store('file')->pull("google_oauth_{$token}");

        if (! $data) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login token expired or already used. Please try again.']);
        }

        if ($data['subdomain'] !== tenancy()->tenant?->subdomain) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Token mismatch. Please try again.']);
        }

        $user = User::find($data['user_id']);

        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'User not found. Please try again.']);
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