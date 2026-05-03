<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        return view('teacher.announcements', [
            'classRooms' => $teacher->teachingClasses()->orderBy('name')->get(),
            'announcements' => Announcement::with(['classRoom'])
                ->where('created_by', $teacher->id)
                ->latest('published_at')
                ->get(),
        ]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        Announcement::create([
            'created_by' => $request->user()->id,
            'class_room_id' => $request->integer('class_room_id'),
            'audience' => $request->input('audience'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('teacher.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }
}
