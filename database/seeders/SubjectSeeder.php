<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjectMap = [
            'Mathematics' => ['Algebra', 'Geometry'],
            'Science' => ['Physics', 'Chemistry'],
            'English' => ['Grammar', 'Literature'],
            'Computer' => ['Programming Basics', 'Office Tools'],
            'Art & Music' => ['Visual Art', 'Music Theory'],
        ];

        foreach ($subjectMap as $className => $subjects) {
            $classRoom = ClassRoom::where('name', $className)->first();

            if (! $classRoom) {
                continue;
            }

            foreach ($subjects as $subjectName) {
                Subject::updateOrCreate(
                    [
                        'class_room_id' => $classRoom->id,
                        'name' => $subjectName,
                    ],
                    []
                );
            }
        }
    }
}
