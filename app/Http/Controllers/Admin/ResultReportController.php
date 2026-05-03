<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultReportController extends Controller
{
    public function index(Request $request): View
    {
        $classRooms = ClassRoom::orderBy('name')->get();

        $exams = Exam::query()
            ->with(['classRoom', 'subject', 'teacher', 'results.student'])
            ->when($request->filled('class_room_id'), fn ($query) => $query->where('class_room_id', $request->integer('class_room_id')))
            ->latest('exam_date')
            ->get();

        return view('admin.results', [
            'classRooms' => $classRooms,
            'exams' => $exams,
            'selectedClassRoomId' => $request->input('class_room_id'),
        ]);
    }
}
