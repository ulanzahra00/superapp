<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeSchoolStatusDefaultToPending extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE schools MODIFY status varchar(255) NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE schools MODIFY status varchar(255) NOT NULL DEFAULT 'active'");
    }
}
