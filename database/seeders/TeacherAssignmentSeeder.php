<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            'teacher1@lms.com' => ['Mathematics'],
            'teacher2@lms.com' => ['Science'],
            'teacher3@lms.com' => ['English'],
            'teacher4@lms.com' => ['Computer'],
            'teacher5@lms.com' => ['Art & Music'],
        ];

        foreach ($assignments as $teacherEmail => $classNames) {
            $teacher = User::where('email', $teacherEmail)->where('role', 'teacher')->first();

            if (! $teacher) {
                continue;
            }

            $classIds = ClassRoom::whereIn('name', $classNames)->pluck('id');

            $teacher->teachingClasses()->syncWithoutDetaching($classIds);
        }
    }
}
