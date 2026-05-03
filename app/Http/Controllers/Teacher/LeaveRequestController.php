<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewLeaveRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $classRoomIds = $teacher->teachingClasses()->pluck('class_rooms.id');

        return view('teacher.leave-requests', [
            'leaveRequests' => LeaveRequest::with(['student', 'requester', 'classRoom', 'reviewer'])
                ->whereIn('class_room_id', $classRoomIds)
                ->latest()
                ->get(),
        ]);
    }

    public function update(ReviewLeaveRequest $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $teacherClassIds = $request->user()->teachingClasses()->pluck('class_rooms.id');

        abort_unless($leaveRequest->class_room_id && $teacherClassIds->contains($leaveRequest->class_room_id), 403);

        $leaveRequest->update([
            'status' => $request->input('status'),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $request->input('review_note'),
        ]);

        return redirect()
            ->route('teacher.leave-requests.index')
            ->with('success', 'Leave request reviewed successfully.');
    }
}
