<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('lms_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_assignment_id')->constrained('lms_assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->text('answer');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['lms_assignment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lms_submissions');
    }
}
