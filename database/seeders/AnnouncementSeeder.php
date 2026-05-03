<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\ClassRoom;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        Announcement::query()->delete();

        $admin = User::query()->where('role', 'admin')->first();

        if ($admin) {
            $items = [
                ['audience' => 'all', 'title' => 'Campus Portal Ready', 'body' => 'Demo data is available across modules so every role can explore the LMS easily.'],
                ['audience' => 'students', 'title' => 'Assignment and Exam Cycle Active', 'body' => 'Students can review new assignments, published results, and the latest class materials.'],
                ['audience' => 'parents', 'title' => 'Parent Progress Access Enabled', 'body' => 'Parents can now monitor attendance, fee dues, results, and full student reports from one place.'],
            ];

            foreach ($items as $index => $item) {
                Announcement::create([
                    'created_by' => $admin->id,
                    'audience' => $item['audience'],
                    'title' => $item['title'],
                    'body' => $item['body'],
                    'published_at' => CarbonImmutable::now()->subDays(5 - $index),
                ]);
            }
        }

        $classRooms = ClassRoom::query()->with('teachers')->orderBy('name')->get();

        foreach ($classRooms as $index => $classRoom) {
            $teacher = $classRoom->teachers->first();

            if (! $teacher) {
                continue;
            }

            Announcement::create([
                'created_by' => $teacher->id,
                'class_room_id' => $classRoom->id,
                'audience' => 'students',
                'title' => $classRoom->name.' Weekly Update',
                'body' => 'Please review the latest materials, complete the project task, and keep an eye on attendance this week.',
                'published_at' => CarbonImmutable::now()->subDays(($index % 3) + 1),
            ]);
        }
    }
}
