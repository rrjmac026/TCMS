<?php
// app/Http/Controllers/Tenant/Admin/AdminBrandingController.php
namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBrandingController extends Controller
{
    public function index()
    {
        $tenant = tenancy()->tenant;

        // Generate the URL in central context before the view renders
        $brandLogo = $tenant->brand_logo
            ? Storage::disk('public')->url($tenant->brand_logo)
            : null;

        return view('tenants.admin.branding.index', compact('tenant', 'brandLogo'));
    }

    public function update(Request $request)
    {
        $tenant = tenancy()->tenant;

        $validated = $request->validate([
            'brand_name'          => ['nullable', 'string', 'max:100'],
            'brand_tagline'       => ['nullable', 'string', 'max:200'],
            'brand_color_primary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'brand_color_accent'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'brand_logo'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
        ]);

        if ($request->hasFile('brand_logo')) {
            // Delete old logo
            if ($tenant->brand_logo) {
                Storage::disk('public')->delete($tenant->brand_logo);
            }
            $path = $request->file('brand_logo')->store("branding/{$tenant->id}", 'public');
            $validated['brand_logo'] = $path;
        }

        // Update on the central DB (tenancy runs on central context here)
        $tenant->update($validated);

        return back()->with('success', 'Branding updated successfully.');
    }

    public function resetLogo()
    {
        $tenant = tenancy()->tenant;
        if ($tenant->brand_logo) {
            Storage::disk('public')->delete($tenant->brand_logo);
            $tenant->update(['brand_logo' => null]);
        }
        return back()->with('success', 'Logo reset to default.');
    }
}