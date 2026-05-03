<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\LearningMaterial;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LearningMaterialTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_upload_material_for_assigned_class(): void
    {
        Storage::fake('local');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Mathematics',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Algebra',
        ]);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);

        $response = $this->actingAs($teacher)->post(route('teacher.materials.store'), [
            'title' => 'Linear Equations PDF',
            'description' => 'Learning notes for students.',
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'file' => UploadedFile::fake()->create('linear-equations.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('teacher.materials'));

        $material = LearningMaterial::where('title', 'Linear Equations PDF')->firstOrFail();

        Storage::disk('local')->assertExists($material->file_path);

        $this->assertDatabaseHas('learning_materials', [
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'original_filename' => 'linear-equations.pdf',
        ]);
    }

    public function test_teacher_cannot_upload_material_for_unassigned_class(): void
    {
        Storage::fake('local');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $classRoom = ClassRoom::create([
            'name' => 'Science',
            'type' => 'optional',
        ]);

        $response = $this->actingAs($teacher)->post(route('teacher.materials.store'), [
            'title' => 'Science PDF',
            'class_room_id' => $classRoom->id,
            'file' => UploadedFile::fake()->create('science.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('class_room_id');
        $this->assertDatabaseMissing('learning_materials', [
            'title' => 'Science PDF',
        ]);
    }

    public function test_student_can_view_and_download_material_for_enrolled_class(): void
    {
        Storage::fake('local');

        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Teacher Anita']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'English',
            'type' => 'optional',
        ]);

        $student->enrolledClasses()->syncWithoutDetaching([$classRoom->id]);
        Storage::disk('local')->put('learning-materials/grammar.pdf', 'PDF content');

        $material = LearningMaterial::create([
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'title' => 'Grammar Notes',
            'disk' => 'local',
            'file_path' => 'learning-materials/grammar.pdf',
            'original_filename' => 'grammar.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 11,
        ]);

        $this->actingAs($student)
            ->get(route('student.materials'))
            ->assertOk()
            ->assertSee('Grammar Notes')
            ->assertSee('Teacher Anita');

        $this->actingAs($student)
            ->get(route('student.materials.download', $material))
            ->assertOk()
            ->assertDownload('grammar.pdf');
    }

    public function test_student_cannot_download_material_for_unenrolled_class(): void
    {
        Storage::fake('local');

        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classRoom = ClassRoom::create([
            'name' => 'Computer',
            'type' => 'optional',
        ]);

        Storage::disk('local')->put('learning-materials/computer.pdf', 'PDF content');

        $material = LearningMaterial::create([
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
            'title' => 'Computer Notes',
            'disk' => 'local',
            'file_path' => 'learning-materials/computer.pdf',
            'original_filename' => 'computer.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 11,
        ]);

        $this->actingAs($student)
            ->get(route('student.materials.download', $material))
            ->assertForbidden();
    }
}
