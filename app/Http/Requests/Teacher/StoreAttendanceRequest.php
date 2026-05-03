<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTeacher() ?? false;
    }

    public function rules(): array
    {
        return [
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'date' => ['required', 'date'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.student_id' => ['required', 'integer', 'exists:users,id'],
            'records.*.status' => ['required', 'in:present,absent,late'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->user()?->teachingClasses()->whereKey($this->integer('class_room_id'))->exists()) {
                    $validator->errors()->add('class_room_id', 'You can only mark attendance for classes assigned to you.');

                    return;
                }

                $classRoom = \App\Models\ClassRoom::with('students')->find($this->integer('class_room_id'));
                $validStudentIds = $classRoom?->students->pluck('id')->all() ?? [];
                $subjectBelongsToClass = \App\Models\Subject::query()
                    ->whereKey($this->integer('subject_id'))
                    ->where('class_room_id', $this->integer('class_room_id'))
                    ->exists();

                if (! $subjectBelongsToClass) {
                    $validator->errors()->add('subject_id', 'Selected subject must belong to the selected class.');

                    return;
                }

                foreach ((array) $this->input('records', []) as $index => $record) {
                    if (! in_array((int) ($record['student_id'] ?? 0), $validStudentIds, true)) {
                        $validator->errors()->add("records.$index.student_id", 'Student must belong to the selected class.');
                    }
                }
            },
        ];
    }
}
