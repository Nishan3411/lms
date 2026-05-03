<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\GradeAssignmentSubmissionRequest;
use App\Http\Requests\Teacher\StoreAssignmentRequest;
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
        $teacher = $request->user();

        $classRooms = $teacher->teachingClasses()
            ->with(['subjects'])
            ->orderBy('name')
            ->get();

        $assignments = Assignment::query()
            ->with(['classRoom', 'subject', 'submissions.student'])
            ->where('teacher_id', $teacher->id)
            ->latest('due_at')
            ->get();

        return view('teacher.assignments', [
            'classRooms' => $classRooms,
            'assignments' => $assignments,
        ]);
    }

    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $attachmentPath = null;
        $attachmentOriginalName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('assignments', 'local');
            $attachmentOriginalName = $file->getClientOriginalName();
        }

        Assignment::create([
            'teacher_id' => $request->user()->id,
            'class_room_id' => $request->integer('class_room_id'),
            'subject_id' => $request->filled('subject_id') ? $request->integer('subject_id') : null,
            'title' => $request->string('title'),
            'description' => $request->input('description'),
            'max_marks' => $request->integer('max_marks'),
            'due_at' => $request->date('due_at'),
            'disk' => 'local',
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
        ]);

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function destroy(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeTeacherAssignment($request, $assignment);

        if ($assignment->attachment_path && Storage::disk($assignment->disk)->exists($assignment->attachment_path)) {
            Storage::disk($assignment->disk)->delete($assignment->attachment_path);
        }

        $assignment->submissions->each(function (AssignmentSubmission $submission): void {
            if ($submission->file_path && Storage::disk($submission->disk)->exists($submission->file_path)) {
                Storage::disk($submission->disk)->delete($submission->file_path);
            }
        });

        $assignment->delete();

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    public function grade(
        GradeAssignmentSubmissionRequest $request,
        AssignmentSubmission $assignmentSubmission
    ): RedirectResponse {
        $assignment = $assignmentSubmission->assignment;
        $this->authorizeTeacherAssignment($request, $assignment);

        $assignmentSubmission->update([
            'marks_obtained' => $request->input('marks_obtained'),
            'teacher_feedback' => $request->input('teacher_feedback'),
            'status' => 'graded',
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Submission reviewed successfully.');
    }

    public function downloadAttachment(Request $request, Assignment $assignment): StreamedResponse
    {
        $this->authorizeTeacherAssignment($request, $assignment);
        abort_unless($assignment->attachment_path, 404);
        abort_unless(Storage::disk($assignment->disk)->exists($assignment->attachment_path), 404);

        return Storage::disk($assignment->disk)
            ->download($assignment->attachment_path, $assignment->attachment_original_name);
    }

    public function downloadSubmission(
        Request $request,
        AssignmentSubmission $assignmentSubmission
    ): StreamedResponse {
        $this->authorizeTeacherAssignment($request, $assignmentSubmission->assignment);
        abort_unless($assignmentSubmission->file_path, 404);
        abort_unless(Storage::disk($assignmentSubmission->disk)->exists($assignmentSubmission->file_path), 404);

        return Storage::disk($assignmentSubmission->disk)
            ->download($assignmentSubmission->file_path, $assignmentSubmission->original_filename);
    }

    private function authorizeTeacherAssignment(Request $request, Assignment $assignment): void
    {
        abort_unless((int) $assignment->teacher_id === (int) $request->user()->id, 403);
    }
}
