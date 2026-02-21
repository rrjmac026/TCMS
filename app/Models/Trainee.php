<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainee extends Model
{
    protected $fillable = [
        // Inherited from User: name, email, password, etc.
        'enrollment_date',     // Trainee-specific
        'student_number',
        'batch_number',
        'learning_path',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'trainee_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'trainee_id');
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'trainee_id');
    }
}
