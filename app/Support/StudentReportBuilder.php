<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class StudentReportBuilder
{
    public function build(User $student): array
    {
        $student->loadMissing([
            'enrolledClasses' => fn ($query) => $query
                ->with(['teachers', 'subjects'])
                ->orderBy('name'),
            'attendanceRecords.attendance.classRoom',
            'attendanceRecords.attendance.subject',
            'examResults.exam.classRoom',
            'examResults.exam.subject',
            'assignmentSubmissions.assignment.classRoom',
            'assignmentSubmissions.assignment.subject',
        ]);

        $attendanceRecords = $student->attendanceRecords
            ->filter(fn ($record) => $record->attendance !== null)
            ->sortByDesc(fn ($record) => $record->attendance->date)
            ->values();

        $examResults = $student->examResults
            ->filter(fn ($result) => $result->exam !== null && $result->exam->isPublished())
            ->sortByDesc(fn ($result) => $result->exam->exam_date)
            ->values();

        $assignmentSubmissions = $student->assignmentSubmissions
            ->filter(fn ($submission) => $submission->assignment !== null)
            ->sortByDesc(fn ($submission) => $submission->reviewed_at ?? $submission->submitted_at ?? $submission->created_at)
            ->values();

        $gradedAssignments = $assignmentSubmissions
            ->filter(fn ($submission) => $submission->marks_obtained !== null)
            ->values();

        $attendanceSummary = $this->attendanceSummary($attendanceRecords);
        $examSummary = $this->examSummary($examResults);
        $assignmentSummary = $this->assignmentSummary($gradedAssignments);
        $subjectPerformance = $this->subjectPerformance($attendanceRecords, $examResults, $gradedAssignments);

        return [
            'attendance' => [
                'summary' => $attendanceSummary,
                'recent_records' => $attendanceRecords->take(6)->values(),
            ],
            'exams' => [
                'summary' => $examSummary,
                'recent_results' => $examResults->take(6)->values(),
            ],
            'assignments' => [
                'summary' => $assignmentSummary,
                'recent_submissions' => $assignmentSubmissions->take(6)->values(),
            ],
            'overview' => [
                'overall_percentage' => $this->overallPercentage($attendanceSummary, $examSummary, $assignmentSummary),
                'classes_count' => $student->enrolledClasses->count(),
                'subjects_count' => $student->enrolledClasses->sum(fn ($classRoom) => $classRoom->subjects->count()),
                'teachers_count' => $student->enrolledClasses->flatMap->teachers->unique('id')->count(),
                'warning' => $attendanceSummary['total'] > 0 && $attendanceSummary['percentage'] < 75
                    ? 'Attendance is below 75%.'
                    : null,
            ],
            'subject_performance' => $subjectPerformance,
        ];
    }

    private function attendanceSummary(Collection $records): array
    {
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();
        $absent = $records->where('status', 'absent')->count();
        $credited = $present + $late;

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'percentage' => $total > 0 ? round(($credited / $total) * 100, 2) : 0,
        ];
    }

    private function examSummary(Collection $results): array
    {
        $count = $results->count();
        $obtained = $results->sum(fn ($result) => (float) $result->marks_obtained);
        $max = $results->sum(fn ($result) => (float) $result->exam->max_marks);

        return [
            'count' => $count,
            'obtained' => round($obtained, 2),
            'max' => round($max, 2),
            'percentage' => $count > 0 && $max > 0 ? round(($obtained / $max) * 100, 2) : null,
        ];
    }

    private function assignmentSummary(Collection $submissions): array
    {
        $count = $submissions->count();
        $obtained = $submissions->sum(fn ($submission) => (float) $submission->marks_obtained);
        $max = $submissions->sum(fn ($submission) => (float) $submission->assignment->max_marks);

        return [
            'count' => $count,
            'obtained' => round($obtained, 2),
            'max' => round($max, 2),
            'percentage' => $count > 0 && $max > 0 ? round(($obtained / $max) * 100, 2) : null,
            'graded' => $count,
        ];
    }

    private function overallPercentage(array $attendanceSummary, array $examSummary, array $assignmentSummary): ?float
    {
        $metrics = collect([
            $attendanceSummary['total'] > 0 ? (float) $attendanceSummary['percentage'] : null,
            $examSummary['count'] > 0 ? (float) $examSummary['percentage'] : null,
            $assignmentSummary['count'] > 0 ? (float) $assignmentSummary['percentage'] : null,
        ])->filter(fn ($value) => $value !== null)->values();

        if ($metrics->isEmpty()) {
            return null;
        }

        return round($metrics->avg(), 2);
    }

    private function subjectPerformance(
        Collection $attendanceRecords,
        Collection $examResults,
        Collection $assignmentSubmissions
    ): Collection {
        $buckets = [];

        foreach ($attendanceRecords as $record) {
            $subject = $record->attendance->subject;
            $classRoom = $record->attendance->classRoom;
            $key = $subject ? 'subject_'.$subject->id : 'class_'.$classRoom->id;

            $buckets[$key] ??= $this->performanceBucket(
                $subject?->name ?? $classRoom->name,
                $classRoom->name
            );

            $buckets[$key]['attendance_total']++;

            if (in_array($record->status, ['present', 'late'], true)) {
                $buckets[$key]['attendance_credited']++;
            }
        }

        foreach ($examResults as $result) {
            $subject = $result->exam->subject;
            $classRoom = $result->exam->classRoom;
            $key = $subject ? 'subject_'.$subject->id : 'class_'.$classRoom->id;

            $buckets[$key] ??= $this->performanceBucket(
                $subject?->name ?? $classRoom->name,
                $classRoom->name
            );

            $buckets[$key]['exam_count']++;
            $buckets[$key]['exam_obtained'] += (float) $result->marks_obtained;
            $buckets[$key]['exam_max'] += (float) $result->exam->max_marks;
        }

        foreach ($assignmentSubmissions as $submission) {
            $subject = $submission->assignment->subject;
            $classRoom = $submission->assignment->classRoom;
            $key = $subject ? 'subject_'.$subject->id : 'class_'.$classRoom->id;

            $buckets[$key] ??= $this->performanceBucket(
                $subject?->name ?? $classRoom->name,
                $classRoom->name
            );

            $buckets[$key]['assignment_count']++;
            $buckets[$key]['assignment_obtained'] += (float) $submission->marks_obtained;
            $buckets[$key]['assignment_max'] += (float) $submission->assignment->max_marks;
        }

        return collect($buckets)
            ->map(function (array $bucket) {
                $attendancePercentage = $bucket['attendance_total'] > 0
                    ? round(($bucket['attendance_credited'] / $bucket['attendance_total']) * 100, 2)
                    : null;
                $examPercentage = $bucket['exam_max'] > 0
                    ? round(($bucket['exam_obtained'] / $bucket['exam_max']) * 100, 2)
                    : null;
                $assignmentPercentage = $bucket['assignment_max'] > 0
                    ? round(($bucket['assignment_obtained'] / $bucket['assignment_max']) * 100, 2)
                    : null;

                $overall = collect([$attendancePercentage, $examPercentage, $assignmentPercentage])
                    ->filter(fn ($value) => $value !== null)
                    ->whenEmpty(fn ($collection) => $collection->push(null))
                    ->filter(fn ($value) => $value !== null)
                    ->avg();

                return [
                    'label' => $bucket['label'],
                    'class_room' => $bucket['class_room'],
                    'attendance_percentage' => $attendancePercentage,
                    'exam_percentage' => $examPercentage,
                    'assignment_percentage' => $assignmentPercentage,
                    'overall_percentage' => $overall !== null ? round($overall, 2) : null,
                    'exam_count' => $bucket['exam_count'],
                    'assignment_count' => $bucket['assignment_count'],
                ];
            })
            ->sortBy([
                ['overall_percentage', 'desc'],
                ['label', 'asc'],
            ])
            ->values();
    }

    private function performanceBucket(string $label, string $classRoom): array
    {
        return [
            'label' => $label,
            'class_room' => $classRoom,
            'attendance_total' => 0,
            'attendance_credited' => 0,
            'exam_count' => 0,
            'exam_obtained' => 0,
            'exam_max' => 0,
            'assignment_count' => 0,
            'assignment_obtained' => 0,
            'assignment_max' => 0,
        ];
    }
}
