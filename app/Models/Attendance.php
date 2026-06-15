<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'student_id', 'date', 'status'];

    protected $casts = ['date' => 'date'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
