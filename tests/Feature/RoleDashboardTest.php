<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_lms_summary_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $classRoom = ClassRoom::create([
            'name' => 'Biology',
            'type' => 'compulsory',
        ]);
        Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Botany',
        ]);
        Topic::create([
            'subject_id' => Subject::firstOrFail()->id,
            'title' => 'Plant Cells',
        ]);
        User::factory()->create(['role' => 'teacher', 'name' => 'Teacher One']);
        User::factory()->create(['role' => 'student', 'name' => 'Student One']);
        User::factory()->create(['role' => 'parent', 'name' => 'Parent One']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Biology');
        $response->assertSee('Teachers');
        $response->assertSee('Students');
    }

    public function test_teacher_dashboard_shows_assigned_classes_and_topics(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Teacher Mary']);
        $classRoom = ClassRoom::create([
            'name' => 'Physics',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Mechanics',
        ]);
        Topic::create([
            'subject_id' => $subject->id,
            'title' => 'Forces',
        ]);
        $student = User::factory()->create(['role' => 'student', 'name' => 'Student Rahul']);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $student->enrolledClasses()->syncWithoutDetaching([$classRoom->id]);

        $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

        $response->assertOk();
        $response->assertSee('Physics');
        $response->assertSee('Mechanics');
        $response->assertSee('Forces');
    }

    public function test_student_dashboard_shows_classes_teachers_topics_and_parents(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Teacher Anita']);
        $parent = User::factory()->create(['role' => 'parent', 'name' => 'Parent Suresh']);
        $classRoom = ClassRoom::create([
            'name' => 'Chemistry',
            'type' => 'compulsory',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Organic Chemistry',
        ]);
        Topic::create([
            'subject_id' => $subject->id,
            'title' => 'Hydrocarbons',
        ]);
        $student = User::factory()->create(['role' => 'student', 'name' => 'Student Neha']);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $parent->children()->syncWithoutDetaching([$student->id]);

        $response = $this->actingAs($student)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Chemistry');
        $response->assertSee('Teacher Anita');
        $response->assertSee('Organic Chemistry');
        $response->assertSee('Hydrocarbons');
        $response->assertSee('Parent Suresh');
    }

    public function test_parent_dashboard_shows_children_and_their_classes(): void
    {
        $parent = User::factory()->create(['role' => 'parent', 'name' => 'Parent Kavita']);
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Teacher Raj']);
        $classRoom = ClassRoom::create([
            'name' => 'English',
            'type' => 'optional',
        ]);
        $subject = Subject::create([
            'class_room_id' => $classRoom->id,
            'name' => 'Grammar',
        ]);
        Topic::create([
            'subject_id' => $subject->id,
            'title' => 'Tenses',
        ]);
        $student = User::factory()->create(['role' => 'student', 'name' => 'Student Meera']);

        $classRoom->teachers()->syncWithoutDetaching([$teacher->id]);
        $student->enrolledClasses()->syncWithoutDetaching([$classRoom->id]);
        $parent->children()->syncWithoutDetaching([$student->id]);

        $response = $this->actingAs($parent)->get(route('parent.dashboard'));

        $response->assertOk();
        $response->assertSee('Student Meera');
        $response->assertSee('English');
        $response->assertSee('Teacher Raj');
    }
}
