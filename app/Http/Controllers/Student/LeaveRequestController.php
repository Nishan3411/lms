<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreLeaveRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        return view('student.leave-requests', [
            'classRooms' => $student->enrolledClasses()->orderBy('name')->get(),
            'leaveRequests' => $student->leaveRequests()
                ->with(['classRoom', 'reviewer'])
                ->latest()
                ->get(),
        ]);
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        LeaveRequest::create([
            'requester_id' => $request->user()->id,
            'student_id' => $request->user()->id,
            'class_room_id' => $request->input('class_room_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'reason' => $request->input('reason'),
        ]);

        return redirect()
            ->route('student.leave-requests.index')
            ->with('success', 'Leave request submitted successfully.');
    }
}
