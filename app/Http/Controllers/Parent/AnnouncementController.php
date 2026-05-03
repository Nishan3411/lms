<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $classIds = $request->user()
            ->children()
            ->with('enrolledClasses')
            ->get()
            ->flatMap->enrolledClasses
            ->pluck('id')
            ->unique();

        $announcements = Announcement::query()
            ->with(['creator', 'classRoom'])
            ->whereNotNull('published_at')
            ->whereIn('audience', ['all', 'parents'])
            ->where(function ($query) use ($classIds): void {
                $query->whereNull('class_room_id')
                    ->orWhereIn('class_room_id', $classIds);
            })
            ->latest('published_at')
            ->get();

        return view('parent.announcements', [
            'announcements' => $announcements,
        ]);
    }
}
