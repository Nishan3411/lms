<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\ScheduleEntry;
use Illuminate\Database\Seeder;

class TimetableSeeder extends Seeder
{
    public function run(): void
    {
        ScheduleEntry::query()->delete();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $slots = [
            ['starts_at' => '09:00', 'ends_at' => '10:00'],
            ['starts_at' => '10:15', 'ends_at' => '11:15'],
            ['starts_at' => '11:30', 'ends_at' => '12:30'],
            ['starts_at' => '13:15', 'ends_at' => '14:15'],
            ['starts_at' => '14:30', 'ends_at' => '15:30'],
        ];

        $classRooms = ClassRoom::query()
            ->with(['subjects', 'teachers'])
            ->orderBy('name')
            ->get();

        foreach ($classRooms as $classIndex => $classRoom) {
            $teacher = $classRoom->teachers->first();

            if (! $teacher || $classRoom->subjects->isEmpty()) {
                continue;
            }

            foreach ($classRoom->subjects->values() as $subjectIndex => $subject) {
                $slot = $slots[($classIndex + $subjectIndex) % count($slots)];

                ScheduleEntry::create([
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'day_of_week' => $days[($classIndex + $subjectIndex) % count($days)],
                    'starts_at' => $slot['starts_at'],
                    'ends_at' => $slot['ends_at'],
                    'location' => 'Room '.(100 + ($classIndex * 10) + $subjectIndex + 1),
                ]);

                ScheduleEntry::create([
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'day_of_week' => $days[($classIndex + $subjectIndex + 2) % count($days)],
                    'starts_at' => $slots[($classIndex + $subjectIndex + 1) % count($slots)]['starts_at'],
                    'ends_at' => $slots[($classIndex + $subjectIndex + 1) % count($slots)]['ends_at'],
                    'location' => 'Room '.(200 + ($classIndex * 10) + $subjectIndex + 1),
                ]);
            }
        }
    }
}
