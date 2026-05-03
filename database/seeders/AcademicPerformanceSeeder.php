<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class AcademicPerformanceSeeder extends Seeder
{
    public function run(): void
    {
        AssignmentSubmission::query()->delete();
        Assignment::query()->delete();
        ExamResult::query()->delete();
        Exam::query()->delete();

        $classRooms = ClassRoom::query()
            ->with(['students', 'subjects', 'teachers'])
            ->orderBy('name')
            ->get();

        foreach ($classRooms as $classIndex => $classRoom) {
            $teacher = $classRoom->teachers->first();

            if (! $teacher || $classRoom->students->isEmpty() || $classRoom->subjects->isEmpty()) {
                continue;
            }

            foreach ($classRoom->subjects->values() as $subjectIndex => $subject) {
                $pastAssignment = Assignment::create([
                    'teacher_id' => $teacher->id,
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $subject->id,
                    'title' => $subject->name.' Practice Set',
                    'description' => 'Seeded graded assignment for demo reporting and performance tracking.',
                    'max_marks' => 25,
                    'due_at' => CarbonImmutable::now()->subDays(18 - $subjectIndex)->setTime(17, 0),
                    'disk' => 'local',
                ]);

                $upcomingAssignment = Assignment::create([
                    'teacher_id' => $teacher->id,
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $subject->id,
                    'title' => $subject->name.' Project Task',
                    'description' => 'Seeded upcoming assignment so dashboards show due work and pending grading.',
                    'max_marks' => 30,
                    'due_at' => CarbonImmutable::now()->addDays(4 + $subjectIndex)->setTime(17, 0),
                    'disk' => 'local',
                ]);

                $publishedExam = Exam::create([
                    'teacher_id' => $teacher->id,
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $subject->id,
                    'title' => $subject->name.' Midterm',
                    'exam_date' => CarbonImmutable::now()->subDays(12 - $subjectIndex)->toDateString(),
                    'max_marks' => 100,
                    'published_at' => CarbonImmutable::now()->subDays(6 - $subjectIndex),
                ]);

                Exam::create([
                    'teacher_id' => $teacher->id,
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $subject->id,
                    'title' => $subject->name.' Final Review',
                    'exam_date' => CarbonImmutable::now()->addDays(10 + $subjectIndex)->toDateString(),
                    'max_marks' => 100,
                ]);

                foreach ($classRoom->students as $studentIndex => $student) {
                    $gradedMarks = $this->boundedScore($student->email, $subject->id, 25, 55, 96);

                    AssignmentSubmission::create([
                        'assignment_id' => $pastAssignment->id,
                        'student_id' => $student->id,
                        'answer_text' => 'Seeded answer for '.$subject->name.' practice work.',
                        'status' => 'graded',
                        'marks_obtained' => round(($gradedMarks / 100) * $pastAssignment->max_marks, 2),
                        'teacher_feedback' => $this->feedbackForPercentage($gradedMarks),
                        'submitted_at' => CarbonImmutable::parse($pastAssignment->due_at)->subDays(1),
                        'reviewed_at' => CarbonImmutable::parse($pastAssignment->due_at)->addDays(2),
                    ]);

                    if (($studentIndex + $classIndex + $subjectIndex) % 4 === 0) {
                        AssignmentSubmission::create([
                            'assignment_id' => $upcomingAssignment->id,
                            'student_id' => $student->id,
                            'answer_text' => 'Seeded draft submission awaiting review.',
                            'status' => 'submitted',
                            'submitted_at' => CarbonImmutable::now()->subDay(),
                        ]);
                    }

                    $examPercentage = $this->boundedScore($student->email, $publishedExam->id, 100, 52, 97);

                    ExamResult::create([
                        'exam_id' => $publishedExam->id,
                        'student_id' => $student->id,
                        'marks_obtained' => round(($examPercentage / 100) * $publishedExam->max_marks, 2),
                        'grade' => $this->gradeForPercentage($examPercentage),
                        'remarks' => $this->remarkForPercentage($examPercentage),
                    ]);
                }
            }
        }
    }

    private function boundedScore(string $seedSource, int $salt, int $scale, int $min, int $max): int
    {
        $range = max(1, $max - $min);

        return $min + (abs(crc32($seedSource.'|'.$salt.'|'.$scale)) % ($range + 1));
    }

    private function gradeForPercentage(int $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }

    private function remarkForPercentage(int $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'Outstanding performance with strong conceptual clarity.',
            $percentage >= 80 => 'Very good work with steady academic consistency.',
            $percentage >= 70 => 'Good progress. A little more revision can lift performance further.',
            $percentage >= 60 => 'Fair performance with room for stronger practice.',
            $percentage >= 50 => 'Basic understanding shown. Needs closer follow-up.',
            default => 'Requires immediate academic support and revision.',
        };
    }

    private function feedbackForPercentage(int $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'Clear, confident submission with excellent accuracy.',
            $percentage >= 80 => 'Strong submission with only minor improvement areas.',
            $percentage >= 70 => 'Good effort. Review a few concepts for stronger precision.',
            $percentage >= 60 => 'Solid attempt. Spend more time on revision and examples.',
            default => 'Submission completed, but the fundamentals need more attention.',
        };
    }
}
