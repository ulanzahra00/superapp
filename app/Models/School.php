<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'npsn',
        'address',
        'phone',
        'email',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getPublicSlugAttribute()
    {
        return self::publicSlugFromName($this->name) ?: $this->slug;
    }

    public static function publicSlugFromName(string $schoolName): string
    {
        $slug = Str::lower(Str::ascii($schoolName));
        $slug = preg_replace('/\bsd\s+negeri\b/', 'sdn', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '', $slug);

        return $slug ?: (Str::slug($schoolName) ?: 'sekolah');
    }
}
