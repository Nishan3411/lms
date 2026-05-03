<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreScheduleEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')->where(
                    fn ($query) => $query->where('class_room_id', $this->integer('class_room_id'))
                ),
            ],
            'teacher_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'teacher'))],
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $teacherAssigned = \App\Models\User::query()
                    ->whereKey($this->integer('teacher_id'))
                    ->where('role', 'teacher')
                    ->whereHas('teachingClasses', fn ($query) => $query->whereKey($this->integer('class_room_id')))
                    ->exists();

                if (! $teacherAssigned) {
                    $validator->errors()->add('teacher_id', 'Teacher must be assigned to the selected class.');
                }
            },
        ];
    }
}
