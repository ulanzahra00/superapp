<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMultiSchoolTenancy extends Migration
{
    private $tenantTables = [
        'users',
        'student_points',
        'sanctions',
        'school_notifications',
        'news',
        'attendances',
        'courses',
        'grades',
        'messages',
        'lms_assignments',
        'lms_submissions',
    ];

    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('npsn')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });

        $defaultSchoolId = DB::table('schools')->insertGetId([
            'name' => 'SD Negeri 1 Molinow',
            'slug' => 'sd-negeri-1-molinow',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->tenantTables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete();
                    $table->index('school_id');
                });
            }
        }

        DB::table('users')->whereNull('school_id')->update(['school_id' => $defaultSchoolId]);

        $this->backfillByUser('student_points', 'student_id');
        $this->backfillByUser('sanctions', 'student_id');
        $this->backfillByUser('school_notifications', 'user_id');
        $this->backfillByUser('news', 'author_id');
        $this->backfillByUser('attendances', 'student_id');
        $this->backfillByUser('courses', 'teacher_id');
        $this->backfillByUser('grades', 'student_id');
        $this->backfillByUser('messages', 'sender_id');
        $this->backfillByUser('lms_assignments', 'teacher_id');
        $this->backfillLmsSubmissions();

        foreach ($this->tenantTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                DB::table($table)->whereNull('school_id')->update(['school_id' => $defaultSchoolId]);
            }
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_nis_unique');
            });
        } catch (\Throwable $exception) {
            //
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique(['school_id', 'nis']);
            });
        } catch (\Throwable $exception) {
            //
        }
    }

    public function down()
    {
        foreach (array_reverse($this->tenantTables) as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('school_id');
                });
            }
        }

        Schema::dropIfExists('schools');
    }

    private function backfillByUser(string $table, string $userColumn): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'school_id')) {
            return;
        }

        DB::statement(
            "UPDATE {$table} target JOIN users u ON target.{$userColumn} = u.id SET target.school_id = u.school_id WHERE target.school_id IS NULL"
        );
    }

    private function backfillLmsSubmissions(): void
    {
        if (! Schema::hasTable('lms_submissions') || ! Schema::hasColumn('lms_submissions', 'school_id')) {
            return;
        }

        DB::statement(
            'UPDATE lms_submissions target JOIN lms_assignments a ON target.lms_assignment_id = a.id SET target.school_id = a.school_id WHERE target.school_id IS NULL'
        );
    }
}
