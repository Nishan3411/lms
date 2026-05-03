<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreLearningMaterialRequest;
use App\Models\LearningMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LearningMaterialController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $classRooms = $teacher->teachingClasses()
            ->with('subjects')
            ->orderBy('name')
            ->get();

        $materials = LearningMaterial::query()
            ->with(['classRoom', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        return view('teacher.materials', [
            'classRooms' => $classRooms,
            'materials' => $materials,
        ]);
    }

    public function store(StoreLearningMaterialRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('learning-materials', 'local');

        LearningMaterial::create([
            'teacher_id' => $request->user()->id,
            'class_room_id' => $request->integer('class_room_id'),
            'subject_id' => $request->filled('subject_id') ? $request->integer('subject_id') : null,
            'title' => $request->string('title'),
            'description' => $request->input('description'),
            'disk' => 'local',
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()
            ->route('teacher.materials')
            ->with('success', 'Learning material uploaded successfully.');
    }

    public function destroy(Request $request, LearningMaterial $learningMaterial): RedirectResponse
    {
        abort_unless((int) $learningMaterial->teacher_id === (int) $request->user()->id, 403);

        if (Storage::disk($learningMaterial->disk)->exists($learningMaterial->file_path)) {
            Storage::disk($learningMaterial->disk)->delete($learningMaterial->file_path);
        }

        $learningMaterial->delete();

        return redirect()
            ->route('teacher.materials')
            ->with('success', 'Learning material deleted successfully.');
    }
}
