<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_id',
        'total_points',
        'sanction_type',
        'note',
        'pdf_path',
        'followed_up_at',
    ];

    protected $casts = [
        'followed_up_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
