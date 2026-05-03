<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreAssignmentSubmissionRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();
        $classIds = $student->enrolledClasses()->pluck('class_rooms.id');

        $assignments = Assignment::query()
            ->with([
                'teacher',
                'classRoom',
                'subject',
                'submissions' => fn ($query) => $query->where('student_id', $student->id),
            ])
            ->whereIn('class_room_id', $classIds)
            ->latest('due_at')
            ->get();

        return view('student.assignments', [
            'assignments' => $assignments,
        ]);
    }

    public function submit(
        StoreAssignmentSubmissionRequest $request,
        Assignment $assignment
    ): RedirectResponse {
        $this->authorizeStudentAssignment($request, $assignment);

        $existingSubmission = AssignmentSubmission::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $request->user()->id)
            ->first();

        $filePath = $existingSubmission?->file_path;
        $originalFilename = $existingSubmission?->original_filename;

        if ($request->hasFile('submission_file')) {
            if ($existingSubmission?->file_path && Storage::disk($existingSubmission->disk)->exists($existingSubmission->file_path)) {
                Storage::disk($existingSubmission->disk)->delete($existingSubmission->file_path);
            }

            $file = $request->file('submission_file');
            $filePath = $file->store('assignment-submissions', 'local');
            $originalFilename = $file->getClientOriginalName();
        }

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $request->user()->id,
            ],
            [
                'answer_text' => $request->input('answer_text'),
                'disk' => 'local',
                'file_path' => $filePath,
                'original_filename' => $originalFilename,
                'status' => 'submitted',
                'marks_obtained' => null,
                'teacher_feedback' => null,
                'submitted_at' => now(),
                'reviewed_at' => null,
            ]
        );

        return redirect()
            ->route('student.assignments.index')
            ->with('success', 'Assignment submitted successfully.');
    }

    public function downloadAttachment(Request $request, Assignment $assignment): StreamedResponse
    {
        $this->authorizeStudentAssignment($request, $assignment);
        abort_unless($assignment->attachment_path, 404);
        abort_unless(Storage::disk($assignment->disk)->exists($assignment->attachment_path), 404);

        return Storage::disk($assignment->disk)
            ->download($assignment->attachment_path, $assignment->attachment_original_name);
    }

    public function downloadSubmission(
        Request $request,
        AssignmentSubmission $assignmentSubmission
    ): StreamedResponse {
        abort_unless((int) $assignmentSubmission->student_id === (int) $request->user()->id, 403);
        abort_unless($assignmentSubmission->file_path, 404);
        abort_unless(Storage::disk($assignmentSubmission->disk)->exists($assignmentSubmission->file_path), 404);

        return Storage::disk($assignmentSubmission->disk)
            ->download($assignmentSubmission->file_path, $assignmentSubmission->original_filename);
    }

    private function authorizeStudentAssignment(Request $request, Assignment $assignment): void
    {
        $canAccess = $request->user()
            ->enrolledClasses()
            ->whereKey($assignment->class_room_id)
            ->exists();

        abort_unless($canAccess, 403);
    }
}
