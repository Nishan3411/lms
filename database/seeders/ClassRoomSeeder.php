<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Mathematics', 'type' => 'compulsory'],
            ['name' => 'Science', 'type' => 'compulsory'],
            ['name' => 'English', 'type' => 'compulsory'],
            ['name' => 'Computer', 'type' => 'compulsory'],
            ['name' => 'Art & Music', 'type' => 'optional'],
        ] as $classRoom) {
            ClassRoom::updateOrCreate(
                ['name' => $classRoom['name']],
                ['type' => $classRoom['type']]
            );
        }
    }
}
