<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_id',
        'teacher_id',
        'type',
        'category',
        'point',
        'title',
        'description',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
