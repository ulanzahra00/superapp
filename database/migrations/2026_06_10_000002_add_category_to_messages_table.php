<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToMessagesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('messages', 'category')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->string('category')->default('personal')->index()->after('receiver_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('messages', 'category')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
}
