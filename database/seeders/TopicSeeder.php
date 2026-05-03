<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $topicMap = [
            'Algebra' => ['Linear Equations', 'Quadratic Expressions', 'Polynomials'],
            'Geometry' => ['Angles and Lines', 'Triangles', 'Coordinate Geometry'],
            'Physics' => ['Motion and Speed', 'Force and Pressure', 'Energy Transfer'],
            'Chemistry' => ['Atomic Structure', 'Chemical Reactions', 'Acids and Bases'],
            'Grammar' => ['Parts of Speech', 'Sentence Structure', 'Active and Passive Voice'],
            'Literature' => ['Poetry Analysis', 'Short Stories', 'Character Study'],
            'Programming Basics' => ['Variables and Data Types', 'Conditionals', 'Loops and Functions'],
            'Office Tools' => ['Word Processing', 'Spreadsheets', 'Presentations'],
            'Visual Art' => ['Color Theory', 'Shading Techniques', 'Perspective Drawing'],
            'Music Theory' => ['Rhythm Patterns', 'Scales and Notes', 'Harmony Basics'],
        ];

        foreach ($topicMap as $subjectName => $topics) {
            $subject = Subject::query()->where('name', $subjectName)->first();

            if (! $subject) {
                continue;
            }

            foreach ($topics as $title) {
                Topic::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'title' => $title,
                    ],
                    []
                );
            }
        }
    }
}
