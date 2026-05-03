<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@lms.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        foreach ($this->teachers() as $teacher) {
            User::updateOrCreate([
                'email' => $teacher['email'],
            ], [
                'name' => $teacher['name'],
                'password' => Hash::make('123456'),
                'role' => 'teacher',
            ]);
        }

        foreach ($this->students() as $student) {
            User::updateOrCreate([
                'email' => $student['email'],
            ], [
                'name' => $student['name'],
                'password' => Hash::make('123456'),
                'role' => 'student',
            ]);
        }

        foreach ($this->parents() as $parent) {
            User::updateOrCreate([
                'email' => $parent['email'],
            ], [
                'name' => $parent['name'],
                'password' => Hash::make('123456'),
                'role' => 'parent',
            ]);
        }
    }

    private function teachers(): array
    {
        return [
            ['name' => 'Anita Teacher', 'email' => 'teacher1@lms.com'],
            ['name' => 'Rakesh Teacher', 'email' => 'teacher2@lms.com'],
            ['name' => 'Meera Teacher', 'email' => 'teacher3@lms.com'],
            ['name' => 'Vikram Teacher', 'email' => 'teacher4@lms.com'],
            ['name' => 'Pooja Teacher', 'email' => 'teacher5@lms.com'],
        ];
    }

    private function students(): array
    {
        return [
            ['name' => 'Rahul Student', 'email' => 'student1@lms.com'],
            ['name' => 'Priya Student', 'email' => 'student2@lms.com'],
            ['name' => 'Arjun Student', 'email' => 'student3@lms.com'],
            ['name' => 'Neha Student', 'email' => 'student4@lms.com'],
            ['name' => 'Karan Student', 'email' => 'student5@lms.com'],
            ['name' => 'Sneha Student', 'email' => 'student6@lms.com'],
            ['name' => 'Rohan Student', 'email' => 'student7@lms.com'],
            ['name' => 'Kavya Student', 'email' => 'student8@lms.com'],
            ['name' => 'Dev Student', 'email' => 'student9@lms.com'],
            ['name' => 'Isha Student', 'email' => 'student10@lms.com'],
            ['name' => 'Aman Student', 'email' => 'student11@lms.com'],
            ['name' => 'Nisha Student', 'email' => 'student12@lms.com'],
            ['name' => 'Yash Student', 'email' => 'student13@lms.com'],
            ['name' => 'Simran Student', 'email' => 'student14@lms.com'],
            ['name' => 'Harsh Student', 'email' => 'student15@lms.com'],
            ['name' => 'Riya Student', 'email' => 'student16@lms.com'],
            ['name' => 'Manav Student', 'email' => 'student17@lms.com'],
            ['name' => 'Diya Student', 'email' => 'student18@lms.com'],
            ['name' => 'Om Student', 'email' => 'student19@lms.com'],
            ['name' => 'Tina Student', 'email' => 'student20@lms.com'],
        ];
    }

    private function parents(): array
    {
        return [
            ['name' => 'Suresh Parent', 'email' => 'parent1@lms.com'],
            ['name' => 'Kavita Parent', 'email' => 'parent2@lms.com'],
            ['name' => 'Mahesh Parent', 'email' => 'parent3@lms.com'],
            ['name' => 'Sunita Parent', 'email' => 'parent4@lms.com'],
            ['name' => 'Rajesh Parent', 'email' => 'parent5@lms.com'],
            ['name' => 'Anjali Parent', 'email' => 'parent6@lms.com'],
            ['name' => 'Deepak Parent', 'email' => 'parent7@lms.com'],
            ['name' => 'Rekha Parent', 'email' => 'parent8@lms.com'],
            ['name' => 'Nitin Parent', 'email' => 'parent9@lms.com'],
            ['name' => 'Bhavna Parent', 'email' => 'parent10@lms.com'],
            ['name' => 'Mukesh Parent', 'email' => 'parent11@lms.com'],
            ['name' => 'Shalini Parent', 'email' => 'parent12@lms.com'],
            ['name' => 'Ajay Parent', 'email' => 'parent13@lms.com'],
            ['name' => 'Pallavi Parent', 'email' => 'parent14@lms.com'],
            ['name' => 'Hemant Parent', 'email' => 'parent15@lms.com'],
        ];
    }
}
