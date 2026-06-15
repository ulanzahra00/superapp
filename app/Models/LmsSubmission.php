<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'lms_assignment_id',
        'school_id',
        'student_id',
        'answer',
        'score',
        'feedback',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(LmsAssignment::class, 'lms_assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
