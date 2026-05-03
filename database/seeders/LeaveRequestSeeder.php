<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    public function run(): void
    {
        LeaveRequest::query()->delete();

        $students = User::query()
            ->where('role', 'student')
            ->with(['enrolledClasses', 'parents'])
            ->orderBy('email')
            ->take(8)
            ->get();

        $admin = User::query()->where('role', 'admin')->first();

        foreach ($students as $index => $student) {
            $classRoom = $student->enrolledClasses->first();

            if (! $classRoom) {
                continue;
            }

            $status = match ($index % 3) {
                0 => 'approved',
                1 => 'rejected',
                default => 'pending',
            };

            $requester = $index % 2 === 0
                ? $student
                : ($student->parents->first() ?? $student);

            LeaveRequest::create([
                'requester_id' => $requester->id,
                'student_id' => $student->id,
                'class_room_id' => $classRoom->id,
                'start_date' => CarbonImmutable::now()->subDays(10 - $index)->toDateString(),
                'end_date' => CarbonImmutable::now()->subDays(9 - $index)->toDateString(),
                'reason' => 'Seeded leave request for dashboard and workflow demonstration.',
                'status' => $status,
                'reviewed_by' => $status === 'pending' ? null : $admin?->id,
                'reviewed_at' => $status === 'pending' ? null : CarbonImmutable::now()->subDays(7 - $index),
                'review_note' => match ($status) {
                    'approved' => 'Approved after schedule review.',
                    'rejected' => 'Please provide additional documentation next time.',
                    default => null,
                },
            ]);
        }
    }
}
