<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('lms_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('class_name')->index();
            $table->string('subject');
            $table->string('type')->default('tugas');
            $table->string('title');
            $table->text('instructions');
            $table->text('question')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lms_assignments');
    }
}
