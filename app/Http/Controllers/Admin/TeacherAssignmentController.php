<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignTeacherToClassRequest;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherAssignmentController extends Controller
{
    public function index(): View
    {
        $teachers = User::where('role', 'teacher')->get();
        $classes = ClassRoom::all();
        $assignments = ClassRoom::with('teachers')->get();

        return view('admin.assign-teacher', compact('teachers', 'classes', 'assignments'));
    }

    public function store(AssignTeacherToClassRequest $request): RedirectResponse
    {
        $class = ClassRoom::findOrFail($request->class_id);
        $teacher = User::where('role', 'teacher')->findOrFail($request->teacher_id);

        $alreadyAssigned = $class->teachers()
            ->whereKey($teacher->id)
            ->exists();

        $class->teachers()->syncWithoutDetaching([$teacher->id]);

        return redirect()
            ->route('admin.assign-teacher')
            ->with(
                'success',
                $alreadyAssigned
                    ? "{$teacher->name} is already assigned to {$class->name}."
                    : 'Teacher assigned successfully.'
            );
    }

    public function destroy(AssignTeacherToClassRequest $request): RedirectResponse
    {
        $class = ClassRoom::findOrFail($request->class_id);
        $teacher = User::where('role', 'teacher')->findOrFail($request->teacher_id);

        $class->teachers()->detach($teacher->id);

        return redirect()
            ->route('admin.assign-teacher')
            ->with('success', "{$teacher->name} removed from {$class->name} successfully.");
    }
}
