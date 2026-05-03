<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignStudentToClassRequest;
use App\Http\Requests\Admin\LinkParentToStudentRequest;
use App\Models\ClassRoom;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(): View
    {
        $students = User::query()
            ->where('role', 'student')
            ->with(['enrolledClasses', 'parents'])
            ->orderBy('name')
            ->get();

        $parents = User::query()
            ->where('role', 'parent')
            ->with(['children'])
            ->orderBy('name')
            ->get();

        $classRooms = ClassRoom::query()
            ->with(['students.parents'])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        return view('admin.enrollment-management', [
            'students' => $students,
            'parents' => $parents,
            'classRooms' => $classRooms,
        ]);
    }

    public function assignStudentToClass(AssignStudentToClassRequest $request): RedirectResponse
    {
        $student = $this->findStudent($request->integer('user_id'));
        $classRoom = $this->findClassRoom($request->integer('class_room_id'));

        $alreadyEnrolled = $student->enrolledClasses()
            ->whereKey($classRoom->id)
            ->exists();

        $student->enrolledClasses()->syncWithoutDetaching([$classRoom->id]);

        $classRoom->fees->each(function ($fee) use ($student): void {
            StudentFee::firstOrCreate(
                [
                    'user_id' => $student->id,
                    'fee_id' => $fee->id,
                ],
                [
                    'total_amount' => $fee->amount,
                    'paid_amount' => 0,
                    'status' => 'pending',
                ]
            );
        });

        return redirect()
            ->route('admin.enrollment')
            ->with(
                'success',
                $alreadyEnrolled
                    ? "{$student->name} is already enrolled in {$classRoom->name}."
                    : "{$student->name} enrolled in {$classRoom->name} successfully."
            );
    }

    public function removeStudentFromClass(AssignStudentToClassRequest $request): RedirectResponse
    {
        $student = $this->findStudent($request->integer('user_id'));
        $classRoom = $this->findClassRoom($request->integer('class_room_id'));

        if ($classRoom->type === 'compulsory') {
            return redirect()
                ->route('admin.enrollment')
                ->with('error', "{$classRoom->name} is a compulsory class and cannot be removed.");
        }

        $student->enrolledClasses()->detach($classRoom->id);

        return redirect()
            ->route('admin.enrollment')
            ->with('success', "{$student->name} removed from {$classRoom->name} successfully.");
    }

    public function assignParentToStudent(LinkParentToStudentRequest $request): RedirectResponse
    {
        $parent = $this->findParent($request->integer('parent_id'));
        $student = $this->findStudent($request->integer('student_id'));

        $alreadyLinked = $parent->children()
            ->whereKey($student->id)
            ->exists();

        $parent->children()->syncWithoutDetaching([$student->id]);

        return redirect()
            ->route('admin.enrollment')
            ->with(
                'success',
                $alreadyLinked
                    ? "{$parent->name} is already linked to {$student->name}."
                    : "{$parent->name} linked to {$student->name} successfully."
            );
    }

    public function unlinkParentFromStudent(LinkParentToStudentRequest $request): RedirectResponse
    {
        $parent = $this->findParent($request->integer('parent_id'));
        $student = $this->findStudent($request->integer('student_id'));

        $parent->children()->detach($student->id);

        return redirect()
            ->route('admin.enrollment')
            ->with('success', "{$parent->name} unlinked from {$student->name} successfully.");
    }

    private function findStudent(int $studentId): User
    {
        return User::query()->where('role', 'student')->findOrFail($studentId);
    }

    private function findParent(int $parentId): User
    {
        return User::query()->where('role', 'parent')->findOrFail($parentId);
    }

    private function findClassRoom(int $classRoomId): ClassRoom
    {
        return ClassRoom::findOrFail($classRoomId);
    }
}
