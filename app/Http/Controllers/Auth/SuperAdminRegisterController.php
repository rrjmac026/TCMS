<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SuperAdminRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'admin_email'  => ['required', 'email', 'unique:tenants,admin_email'],
            'subdomain'    => ['required', 'string', 'alpha_dash', 'unique:tenants,subdomain'],
            'subscription' => ['required', 'in:basic,standard,premium'],
        ]);

        try {
            Tenant::create([
                'id'           => Str::uuid()->toString(),
                'name'         => $request->name,
                'admin_email'  => $request->admin_email,
                'subdomain'    => strtolower($request->subdomain),
                'subscription' => $request->subscription,
                'status'       => 'pending',
                'expires_at'   => null,
            ]);

            // Notify superadmin
            $superadmin = User::where('role', 'superadmin')->first();
            if ($superadmin) {
                Notification::create([
                    'user_id' => $superadmin->id,
                    'title'   => 'New Tenant Application',
                    'message' => "A new tenant '{$request->name}' has applied for tenancy with email {$request->admin_email}.",
                    'link'    => route('superadmin.tenants.index'),
                ]);
            }

            return redirect()->route('register')
                ->with('status', 'submitted');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error during registration: ' . $e->getMessage());
        }
    }
}