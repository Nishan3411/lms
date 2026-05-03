<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_room_user', function (Blueprint $table) {
            $table->unique(['class_room_id', 'user_id'], 'class_room_user_unique_enrollment');
        });

        Schema::table('parent_student', function (Blueprint $table) {
            $table->unique(['parent_id', 'student_id'], 'parent_student_unique_link');
        });

        Schema::table('class_room_teacher', function (Blueprint $table) {
            $table->unique(['class_room_id', 'teacher_id'], 'class_room_teacher_unique_assignment');
        });
    }

    public function down(): void
    {
        Schema::table('class_room_user', function (Blueprint $table) {
            $table->dropUnique('class_room_user_unique_enrollment');
        });

        Schema::table('parent_student', function (Blueprint $table) {
            $table->dropUnique('parent_student_unique_link');
        });

        Schema::table('class_room_teacher', function (Blueprint $table) {
            $table->dropUnique('class_room_teacher_unique_assignment');
        });
    }
};
