<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LearningMaterialController extends Controller
{
    public function index(Request $request): View
    {
        $classIds = $request->user()
            ->enrolledClasses()
            ->pluck('class_rooms.id');

        $materials = LearningMaterial::query()
            ->with(['teacher', 'classRoom', 'subject'])
            ->whereIn('class_room_id', $classIds)
            ->latest()
            ->get();

        return view('student.materials', [
            'materials' => $materials,
        ]);
    }

    public function download(Request $request, LearningMaterial $learningMaterial): StreamedResponse
    {
        $canDownload = $request->user()
            ->enrolledClasses()
            ->whereKey($learningMaterial->class_room_id)
            ->exists();

        abort_unless($canDownload, 403);
        abort_unless(Storage::disk($learningMaterial->disk)->exists($learningMaterial->file_path), 404);

        return Storage::disk($learningMaterial->disk)
            ->download($learningMaterial->file_path, $learningMaterial->original_filename);
    }
}
