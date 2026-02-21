<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminTrainersManagementController extends Controller
{
    /**
     * Display a listing of trainers.
     */
    public function index(Request $request)
    {
        $query = Trainer::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('certification_number', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $trainers    = $query->latest()->paginate(10)->withQueryString();
        $departments = Trainer::select('department')->distinct()->whereNotNull('department')->pluck('department');

        return view('admin.trainers.index', compact('trainers', 'departments'));
    }

    /**
     * Show the form for creating a new trainer.
     */
    public function create()
    {
        return view('admin.trainers.create');
    }

    /**
     * Store a newly created trainer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'email', 'unique:users,email'],
            'password'               => ['required', 'confirmed', Rules\Password::defaults()],
            'specialization'         => ['nullable', 'string', 'max:255'],
            'certification_number'   => ['nullable', 'string', 'max:255'],
            'experience_years'       => ['nullable', 'integer', 'min:0', 'max:60'],
            'department'             => ['nullable', 'string', 'max:255'],
        ]);

        Trainer::create([
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'password'             => Hash::make($validated['password']),
            'role'                 => 'trainer',
            'specialization'       => $validated['specialization'],
            'certification_number' => $validated['certification_number'],
            'experience_years'     => $validated['experience_years'],
            'department'           => $validated['department'],
        ]);

        return redirect()->route('admin.trainers.index')
                         ->with('success', 'Trainer created successfully.');
    }

    /**
     * Display the specified trainer.
     */
    public function show(Trainer $trainer)
    {
        $trainer->load(['assessments', 'attendanceRecords', 'classSchedules']);

        return view('admin.trainers.show', compact('trainer'));
    }

    /**
     * Show the form for editing the specified trainer.
     */
    public function edit(Trainer $trainer)
    {
        return view('admin.trainers.edit', compact('trainer'));
    }

    /**
     * Update the specified trainer.
     */
    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'email', 'unique:users,email,' . $trainer->id],
            'password'               => ['nullable', 'confirmed', Rules\Password::defaults()],
            'specialization'         => ['nullable', 'string', 'max:255'],
            'certification_number'   => ['nullable', 'string', 'max:255'],
            'experience_years'       => ['nullable', 'integer', 'min:0', 'max:60'],
            'department'             => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'specialization'       => $validated['specialization'],
            'certification_number' => $validated['certification_number'],
            'experience_years'     => $validated['experience_years'],
            'department'           => $validated['department'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $trainer->update($data);

        return redirect()->route('admin.trainers.index')
                         ->with('success', 'Trainer updated successfully.');
    }

    /**
     * Remove the specified trainer.
     */
    public function destroy(Trainer $trainer)
    {
        $trainer->delete();

        return redirect()->route('admin.trainers.index')
                         ->with('success', 'Trainer deleted successfully.');
    }
}