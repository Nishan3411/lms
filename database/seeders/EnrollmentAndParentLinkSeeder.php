<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentAndParentLinkSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::query()
            ->where('role', 'student')
            ->orderBy('email')
            ->get();

        $parents = User::query()
            ->where('role', 'parent')
            ->orderBy('email')
            ->get();

        $compulsoryClassIds = ClassRoom::query()
            ->where('type', 'compulsory')
            ->pluck('id');

        $optionalClass = ClassRoom::query()
            ->where('type', 'optional')
            ->orderBy('name')
            ->first();

        foreach ($students as $index => $student) {
            if ($compulsoryClassIds->isNotEmpty()) {
                $student->enrolledClasses()->syncWithoutDetaching($compulsoryClassIds);
            }

            if ($optionalClass && $index < 8) {
                $student->enrolledClasses()->syncWithoutDetaching([$optionalClass->id]);
            }
        }

        if ($students->isEmpty() || $parents->isEmpty()) {
            return;
        }

        foreach ($students as $index => $student) {
            $primaryParent = $parents[$index % $parents->count()];
            $primaryParent->children()->syncWithoutDetaching([$student->id]);
        }

        foreach ($parents as $index => $parent) {
            $studentIds = [$students[$index % $students->count()]->id];

            if ($index < 5) {
                $studentIds[] = $students[($index + 10) % $students->count()]->id;
            }

            $parent->children()->syncWithoutDetaching(array_unique($studentIds));
        }
    }
}
