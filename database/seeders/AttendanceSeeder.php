<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $classRooms = ClassRoom::query()
            ->with(['students', 'subjects'])
            ->orderBy('name')
            ->get();

        $dates = $this->attendanceDates();

        foreach ($classRooms as $classRoom) {
            if ($classRoom->students->isEmpty() || $classRoom->subjects->isEmpty()) {
                continue;
            }

            Attendance::query()
                ->where('class_room_id', $classRoom->id)
                ->delete();

            foreach ($classRoom->subjects as $subject) {
                foreach ($dates as $date) {
                    $attendance = Attendance::create([
                        'class_room_id' => $classRoom->id,
                        'subject_id' => $subject->id,
                        'date' => $date->toDateString(),
                    ]);

                    foreach ($classRoom->students as $student) {
                        AttendanceRecord::create([
                            'attendance_id' => $attendance->id,
                            'student_id' => $student->id,
                            'status' => $this->statusFor($student->email, $classRoom->id, $subject->id, $date),
                        ]);
                    }
                }
            }
        }
    }

    private function attendanceDates(): array
    {
        $dates = [];
        $cursor = CarbonImmutable::now()->subWeeks(8)->startOfDay();

        while (count($dates) < 30) {
            if ($cursor->isWeekday()) {
                $dates[] = $cursor;
            }

            $cursor = $cursor->addDay();
        }

        return $dates;
    }

    private function statusFor(string $studentEmail, int $classRoomId, int $subjectId, CarbonImmutable $date): string
    {
        $targetRate = 40 + (abs(crc32($studentEmail)) % 61);
        $dailySeed = abs(crc32($studentEmail.'|'.$classRoomId.'|'.$subjectId.'|'.$date->toDateString()));
        $roll = $dailySeed % 100;

        if ($roll < max(5, $targetRate - 8)) {
            return 'present';
        }

        if ($roll < $targetRate) {
            return 'late';
        }

        return 'absent';
    }
}
