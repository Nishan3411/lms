<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $classIds = $request->user()->enrolledClasses()->pluck('class_rooms.id');

        $announcements = Announcement::query()
            ->with(['creator', 'classRoom'])
            ->whereNotNull('published_at')
            ->whereIn('audience', ['all', 'students'])
            ->where(function ($query) use ($classIds): void {
                $query->whereNull('class_room_id')
                    ->orWhereIn('class_room_id', $classIds);
            })
            ->latest('published_at')
            ->get();

        return view('student.announcements', [
            'announcements' => $announcements,
        ]);
    }
}
