<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function index(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->with([
                'enrolledClasses.scheduleEntries.subject',
                'enrolledClasses.scheduleEntries.teacher',
            ])
            ->orderBy('name')
            ->get();

        return view('parent.timetable', [
            'children' => $children,
        ]);
    }
}
