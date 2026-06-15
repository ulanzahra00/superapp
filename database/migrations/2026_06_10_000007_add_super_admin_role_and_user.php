<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddSuperAdminRoleAndUser extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY role enum('super_admin','admin','guru','siswa','orang_tua') NOT NULL DEFAULT 'siswa'");

        $schoolId = DB::table('schools')->where('status', 'active')->orderBy('id')->value('id');

        if (! $schoolId) {
            $schoolId = DB::table('schools')->insertGetId([
                'name' => 'Sekolah Pusat',
                'slug' => 'sekolah-pusat',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')->updateOrInsert(
            ['email' => 'superadmin@sekolah.test'],
            [
                'school_id' => $schoolId,
                'name' => 'Super Admin Pusat',
                'role' => 'super_admin',
                'password' => Hash::make('password'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down()
    {
        DB::table('users')->where('email', 'superadmin@sekolah.test')->delete();
        DB::statement("ALTER TABLE users MODIFY role enum('admin','guru','siswa','orang_tua') NOT NULL DEFAULT 'siswa'");
    }
}
