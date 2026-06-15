<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageUrlToNewsTable extends Migration
{
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('cover_color');
        });
    }

    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
}
