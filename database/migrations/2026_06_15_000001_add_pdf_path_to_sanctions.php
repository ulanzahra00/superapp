<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfPathToSanctions extends Migration
{
    public function up()
    {
        Schema::table('sanctions', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('note');
        });
    }

    public function down()
    {
        Schema::table('sanctions', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
}