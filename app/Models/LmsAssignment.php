<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'school_id',
        'class_name',
        'subject',
        'type',
        'title',
        'instructions',
        'question',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function submissions()
    {
        return $this->hasMany(LmsSubmission::class);
    }
}
