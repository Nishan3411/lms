<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\LearningMaterial;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LearningMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'teacher_email' => 'teacher1@lms.com',
                'class_name' => 'Mathematics',
                'subject_name' => 'Algebra',
                'title' => 'Algebra Basics PDF',
                'description' => 'Introductory algebra notes for students.',
                'filename' => 'algebra-basics.pdf',
                'mime_type' => 'application/pdf',
                'content' => $this->samplePdf('Algebra Basics'),
            ],
            [
                'teacher_email' => 'teacher1@lms.com',
                'class_name' => 'Mathematics',
                'subject_name' => 'Geometry',
                'title' => 'Geometry Shapes PPTX',
                'description' => 'Presentation on basic geometric shapes.',
                'filename' => 'geometry-shapes.pptx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'content' => $this->samplePresentation('Geometry Shapes'),
            ],
            [
                'teacher_email' => 'teacher2@lms.com',
                'class_name' => 'Science',
                'subject_name' => 'Physics',
                'title' => 'Motion Concepts PDF',
                'description' => 'Physics concepts covering motion and force.',
                'filename' => 'motion-concepts.pdf',
                'mime_type' => 'application/pdf',
                'content' => $this->samplePdf('Motion Concepts'),
            ],
            [
                'teacher_email' => 'teacher2@lms.com',
                'class_name' => 'Science',
                'subject_name' => 'Chemistry',
                'title' => 'Matter States PPTX',
                'description' => 'Presentation on solids, liquids, and gases.',
                'filename' => 'matter-states.pptx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'content' => $this->samplePresentation('States of Matter'),
            ],
            [
                'teacher_email' => 'teacher3@lms.com',
                'class_name' => 'English',
                'subject_name' => 'Grammar',
                'title' => 'Grammar Rules PDF',
                'description' => 'Grammar essentials and sentence structure notes.',
                'filename' => 'grammar-rules.pdf',
                'mime_type' => 'application/pdf',
                'content' => $this->samplePdf('Grammar Rules'),
            ],
            [
                'teacher_email' => 'teacher3@lms.com',
                'class_name' => 'English',
                'subject_name' => 'Literature',
                'title' => 'Poetry Elements PPTX',
                'description' => 'Presentation covering imagery, tone, and rhyme.',
                'filename' => 'poetry-elements.pptx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'content' => $this->samplePresentation('Poetry Elements'),
            ],
            [
                'teacher_email' => 'teacher4@lms.com',
                'class_name' => 'Computer',
                'subject_name' => 'Programming Basics',
                'title' => 'Programming Basics PDF',
                'description' => 'Starter notes on logic, syntax, and variables.',
                'filename' => 'programming-basics.pdf',
                'mime_type' => 'application/pdf',
                'content' => $this->samplePdf('Programming Basics'),
            ],
            [
                'teacher_email' => 'teacher4@lms.com',
                'class_name' => 'Computer',
                'subject_name' => 'Office Tools',
                'title' => 'Office Tools PPTX',
                'description' => 'Slides about common office productivity software.',
                'filename' => 'office-tools.pptx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'content' => $this->samplePresentation('Office Tools'),
            ],
            [
                'teacher_email' => 'teacher5@lms.com',
                'class_name' => 'Art & Music',
                'subject_name' => 'Visual Art',
                'title' => 'Color Theory PDF',
                'description' => 'Basic color theory reference notes.',
                'filename' => 'color-theory.pdf',
                'mime_type' => 'application/pdf',
                'content' => $this->samplePdf('Color Theory'),
            ],
            [
                'teacher_email' => 'teacher5@lms.com',
                'class_name' => 'Art & Music',
                'subject_name' => 'Music Theory',
                'title' => 'Music Theory PPTX',
                'description' => 'Presentation on rhythm, notes, and scales.',
                'filename' => 'music-theory.pptx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'content' => $this->samplePresentation('Music Theory'),
            ],
        ];

        foreach ($materials as $material) {
            $teacher = User::where('email', $material['teacher_email'])->where('role', 'teacher')->first();
            $classRoom = ClassRoom::where('name', $material['class_name'])->first();
            $subject = Subject::where('name', $material['subject_name'])
                ->where('class_room_id', $classRoom?->id)
                ->first();

            if (! $teacher || ! $classRoom || ! $subject) {
                continue;
            }

            $path = 'learning-materials/seed/'.$material['filename'];

            Storage::disk('local')->put($path, $material['content']);

            LearningMaterial::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'title' => $material['title'],
                ],
                [
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $subject->id,
                    'description' => $material['description'],
                    'disk' => 'local',
                    'file_path' => $path,
                    'original_filename' => $material['filename'],
                    'mime_type' => $material['mime_type'],
                    'file_size' => strlen($material['content']),
                ]
            );
        }
    }

    private function samplePdf(string $title): string
    {
        return "%PDF-1.4\n".
            "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n".
            "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n".
            "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 300 144]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n".
            "4 0 obj<</Length 55>>stream\n".
            "BT /F1 18 Tf 30 90 Td (".$title.") Tj ET\n".
            "endstream\n".
            "endobj\n".
            "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n".
            "xref\n0 6\n0000000000 65535 f \n".
            "trailer<</Size 6/Root 1 0 R>>\nstartxref\n0\n%%EOF";
    }

    private function samplePresentation(string $title): string
    {
        return "Sample presentation file for ".$title.". This is seeded demo content for LMS materials.";
    }
}
