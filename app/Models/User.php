<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'parent_id',
        'nis',
        'class_name',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function studentPoints()
    {
        return $this->hasMany(StudentPoint::class, 'student_id');
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(SchoolNotification::class);
    }

    public function characterScore()
    {
        return (int) $this->studentPoints()->sum('point');
    }

    public function hasRole($roles)
    {
        return in_array($this->role, (array) $roles, true);
    }
}
