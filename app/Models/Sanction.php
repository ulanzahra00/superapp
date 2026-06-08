<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_points',
        'sanction_type',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
