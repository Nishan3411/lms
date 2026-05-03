<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        $this->call([
            ClassRoomSeeder::class,
            SubjectSeeder::class,
            TopicSeeder::class,
            UserSeeder::class,
            TeacherAssignmentSeeder::class,
            EnrollmentAndParentLinkSeeder::class,
            FeeStructureSeeder::class,
            TimetableSeeder::class,
            LearningMaterialSeeder::class,
            AcademicPerformanceSeeder::class,
            AttendanceSeeder::class,
            AnnouncementSeeder::class,
            LeaveRequestSeeder::class,
        ]);

    }
}
