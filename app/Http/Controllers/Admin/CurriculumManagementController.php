<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumManagementController extends Controller
{
    public function index(): View
    {
        $classRooms = ClassRoom::with(['subjects.topics'])
            ->withCount(['subjects', 'students', 'teachers'])
            ->orderBy('name')
            ->get();

        $subjects = Subject::with('classRoom')
            ->withCount('topics')
            ->orderBy('name')
            ->get();

        return view('admin.curriculum', [
            'classRooms' => $classRooms,
            'subjects' => $subjects,
        ]);
    }

    public function storeClassRoom(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:class_rooms,name'],
            'type' => ['required', Rule::in(['compulsory', 'optional'])],
        ]);

        ClassRoom::create($validated);

        return redirect()->route('admin.curriculum')->with('success', 'Class created successfully.');
    }

    public function updateClassRoom(Request $request, ClassRoom $classRoom): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('class_rooms', 'name')->ignore($classRoom->id)],
            'type' => ['required', Rule::in(['compulsory', 'optional'])],
        ]);

        $classRoom->update($validated);

        return redirect()->route('admin.curriculum')->with('success', 'Class updated successfully.');
    }

    public function destroyClassRoom(ClassRoom $classRoom): RedirectResponse
    {
        $classRoom->delete();

        return redirect()->route('admin.curriculum')->with('success', 'Class deleted successfully.');
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_room_id' => ['required', 'exists:class_rooms,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')->where(
                    fn ($query) => $query->where('class_room_id', $request->integer('class_room_id'))
                ),
            ],
        ]);

        Subject::create($validated);

        return redirect()->route('admin.curriculum')->with('success', 'Subject created successfully.');
    }

    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'class_room_id' => ['required', 'exists:class_rooms,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')
                    ->where(fn ($query) => $query->where('class_room_id', $request->integer('class_room_id')))
                    ->ignore($subject->id),
            ],
        ]);

        $subject->update($validated);

        return redirect()->route('admin.curriculum')->with('success', 'Subject updated successfully.');
    }

    public function destroySubject(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('admin.curriculum')->with('success', 'Subject deleted successfully.');
    }

    public function storeTopic(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics', 'title')->where(
                    fn ($query) => $query->where('subject_id', $request->integer('subject_id'))
                ),
            ],
        ]);

        Topic::create($validated);

        return redirect()->route('admin.curriculum')->with('success', 'Topic created successfully.');
    }

    public function updateTopic(Request $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics', 'title')
                    ->where(fn ($query) => $query->where('subject_id', $request->integer('subject_id')))
                    ->ignore($topic->id),
            ],
        ]);

        $topic->update($validated);

        return redirect()->route('admin.curriculum')->with('success', 'Topic updated successfully.');
    }

    public function destroyTopic(Topic $topic): RedirectResponse
    {
        $topic->delete();

        return redirect()->route('admin.curriculum')->with('success', 'Topic deleted successfully.');
    }
}
