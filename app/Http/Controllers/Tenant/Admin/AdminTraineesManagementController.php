<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminTraineesManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'trainee');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $trainees = $query->latest()->paginate(10)->withQueryString();

        return view('tenants.admin.trainees.index', compact('trainees'));
    }

    public function create()
    {
        return view('tenants.admin.trainees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'trainee',
        ]);

        $limit = request()->attributes->get('trainee_limit');
        if ($limit) {
            $count = User::where('role', 'trainee')->count();
            if ($count >= $limit) {
                return back()->withErrors(['limit' => "Your plan allows a maximum of {$limit} trainees."]);
            }
        }

        return redirect()->route('tenants.admin.trainees.index')
                         ->with('success', 'Trainee created successfully.');
    }

    public function show(User $trainee)
    {
        $trainee->load(['enrollments.course', 'enrollments.attendanceRecords']);

        return view('tenants.admin.trainees.show', compact('trainee'));
    }

    public function edit(User $trainee)
    {
        return view('tenants.admin.trainees.edit', compact('trainee'));
    }

    public function update(Request $request, User $trainee)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $trainee->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $trainee->update($data);

        return redirect()->route('tenants.admin.trainees.index')
                         ->with('success', 'Trainee updated successfully.');
    }

    public function destroy(User $trainee)
    {
        $trainee->delete();

        return redirect()->route('tenants.admin.trainees.index')
                         ->with('success', 'Trainee deleted successfully.');
    }
}