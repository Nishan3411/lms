<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (! Schema::hasColumn('attendances', 'subject_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreignId('subject_id')
                    ->nullable()
                    ->after('class_room_id')
                    ->constrained()
                    ->nullOnDelete();
            });
        }

        DB::table('attendances')
            ->orderBy('id')
            ->get()
            ->each(function ($attendance): void {
                $subjectId = DB::table('subjects')
                    ->where('class_room_id', $attendance->class_room_id)
                    ->orderBy('id')
                    ->value('id');

                if ($subjectId) {
                    DB::table('attendances')
                        ->where('id', $attendance->id)
                        ->update(['subject_id' => $subjectId]);
                }
            });

        if ($driver === 'sqlite') {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unique(
                    ['class_room_id', 'subject_id', 'date'],
                    'attendances_unique_per_class_subject_day'
                );
                $table->dropUnique('attendances_unique_per_class_day');
            });

            return;
        }

        if (! $this->indexExists('attendances', 'attendances_unique_per_class_subject_day')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unique(
                    ['class_room_id', 'subject_id', 'date'],
                    'attendances_unique_per_class_subject_day'
                );
            });
        }

        if ($this->indexExists('attendances', 'attendances_unique_per_class_day')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('attendances_unique_per_class_day');
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('attendances_unique_per_class_subject_day');
                $table->dropConstrainedForeignId('subject_id');
                $table->unique(['class_room_id', 'date'], 'attendances_unique_per_class_day');
            });

            return;
        }

        if ($this->indexExists('attendances', 'attendances_unique_per_class_subject_day')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('attendances_unique_per_class_subject_day');
            });
        }

        if (Schema::hasColumn('attendances', 'subject_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropConstrainedForeignId('subject_id');
            });
        }

        if (! $this->indexExists('attendances', 'attendances_unique_per_class_day')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unique(['class_room_id', 'date'], 'attendances_unique_per_class_day');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
