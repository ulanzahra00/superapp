<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolCoreTables extends Migration
{
    public function up()
    {
        Schema::create('student_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['prestasi', 'pelanggaran']);
            $table->enum('category', ['Disiplin', 'Tanggung Jawab', 'Kejujuran', 'Kerjasama']);
            $table->integer('point');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('occurred_at');
            $table->timestamps();
        });

        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->integer('total_points');
            $table->string('sanction_type');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('school_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('level')->default('info');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Sekolah');
            $table->string('cover_color')->default('emerald');
            $table->string('image_url')->nullable();
            $table->text('excerpt');
            $table->longText('content');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->default('hadir');
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('class_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->string('semester')->default('Ganjil');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->string('category')->default('personal')->index();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('news');
        Schema::dropIfExists('school_notifications');
        Schema::dropIfExists('sanctions');
        Schema::dropIfExists('student_points');
    }
}
