<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\ClassRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        return view('admin.announcements', [
            'classRooms' => ClassRoom::orderBy('name')->get(),
            'announcements' => Announcement::with(['creator', 'classRoom'])
                ->latest('published_at')
                ->get(),
        ]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        Announcement::create([
            'created_by' => $request->user()->id,
            'class_room_id' => $request->filled('class_room_id') ? $request->integer('class_room_id') : null,
            'audience' => $request->input('audience'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }
}
