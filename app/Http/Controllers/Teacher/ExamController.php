<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreExamRequest;
use App\Http\Requests\Teacher\StoreExamResultsRequest;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $classRooms = $teacher->teachingClasses()
            ->with(['subjects', 'students'])
            ->orderBy('name')
            ->get();

        $exams = Exam::query()
            ->with(['classRoom.students', 'subject', 'results.student'])
            ->where('teacher_id', $teacher->id)
            ->latest('exam_date')
            ->get();

        return view('teacher.exams', [
            'classRooms' => $classRooms,
            'exams' => $exams,
        ]);
    }

    public function store(StoreExamRequest $request): RedirectResponse
    {
        Exam::create([
            'teacher_id' => $request->user()->id,
            'class_room_id' => $request->integer('class_room_id'),
            'subject_id' => $request->integer('subject_id'),
            'title' => $request->input('title'),
            'exam_date' => $request->date('exam_date'),
            'max_marks' => $request->integer('max_marks'),
        ]);

        return redirect()
            ->route('teacher.exams.index')
            ->with('success', 'Exam scheduled successfully.');
    }

    public function storeResults(StoreExamResultsRequest $request, Exam $exam): RedirectResponse
    {
        $this->authorizeTeacherExam($request, $exam);

        foreach ($request->input('results', []) as $result) {
            if ($result['marks_obtained'] === null || $result['marks_obtained'] === '') {
                continue;
            }

            ExamResult::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $result['student_id'],
                ],
                [
                    'marks_obtained' => $result['marks_obtained'],
                    'grade' => $this->gradeFor((float) $result['marks_obtained'], (float) $exam->max_marks),
                    'remarks' => $result['remarks'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('teacher.exams.index')
            ->with('success', 'Exam marks saved successfully.');
    }

    public function publish(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeTeacherExam($request, $exam);

        $exam->update(['published_at' => now()]);

        return redirect()
            ->route('teacher.exams.index')
            ->with('success', 'Exam results published successfully.');
    }

    private function authorizeTeacherExam(Request $request, Exam $exam): void
    {
        abort_unless((int) $exam->teacher_id === (int) $request->user()->id, 403);
    }

    private function gradeFor(float $marks, float $maxMarks): string
    {
        $percentage = $maxMarks > 0 ? ($marks / $maxMarks) * 100 : 0;

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }
}
