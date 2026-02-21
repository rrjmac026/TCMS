<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class AdminCertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['enrollment.trainee', 'enrollment.course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('certificate_number', 'like', "%{$search}%")
                  ->orWhereHas('enrollment.trainee', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('enrollment.course', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('expired')) {
            if ($request->expired === 'yes') {
                $query->whereNotNull('expires_at')->whereDate('expires_at', '<', now());
            } elseif ($request->expired === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhereDate('expires_at', '>=', now());
                });
            }
        }

        $certificates = $query->latest('issued_at')->paginate(10)->withQueryString();

        return view('admin.certificates.index', compact('certificates'));
    }

    public function create()
    {
        // Only completed enrollments can receive a certificate
        $enrollments = Enrollment::with(['trainee', 'course'])
                                 ->where('status', 'completed')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        return view('admin.certificates.create', compact('enrollments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id'      => ['required', 'exists:enrollments,id'],
            'certificate_number' => ['required', 'string', 'unique:certificates,certificate_number'],
            'issued_at'          => ['required', 'date'],
            'expires_at'         => ['nullable', 'date', 'after:issued_at'],
        ]);

        // One certificate per enrollment only
        $exists = Certificate::where('enrollment_id', $validated['enrollment_id'])->exists();

        if ($exists) {
            return back()->withInput()
                         ->withErrors(['enrollment_id' => 'A certificate for this enrollment already exists.']);
        }

        Certificate::create($validated);

        return redirect()->route('admin.certificates.index')
                         ->with('success', 'Certificate issued successfully.');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['enrollment.trainee', 'enrollment.course']);

        return view('admin.certificates.show', compact('certificate'));
    }

    public function edit(Certificate $certificate)
    {
        $enrollments = Enrollment::with(['trainee', 'course'])
                                 ->where('status', 'completed')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        return view('admin.certificates.edit', compact('certificate', 'enrollments'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'enrollment_id'      => ['required', 'exists:enrollments,id'],
            'certificate_number' => ['required', 'string', 'unique:certificates,certificate_number,' . $certificate->id],
            'issued_at'          => ['required', 'date'],
            'expires_at'         => ['nullable', 'date', 'after:issued_at'],
        ]);

        // Prevent duplicate but exclude current record
        $exists = Certificate::where('enrollment_id', $validated['enrollment_id'])
                             ->where('id', '!=', $certificate->id)
                             ->exists();

        if ($exists) {
            return back()->withInput()
                         ->withErrors(['enrollment_id' => 'A certificate for this enrollment already exists.']);
        }

        $certificate->update($validated);

        return redirect()->route('admin.certificates.index')
                         ->with('success', 'Certificate updated successfully.');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('admin.certificates.index')
                         ->with('success', 'Certificate deleted successfully.');
    }
}