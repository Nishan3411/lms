<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTeacher() ?? false;
    }

    public function rules(): array
    {
        return [
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'audience' => ['required', 'in:students,parents'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->user()?->teachingClasses()->whereKey($this->integer('class_room_id'))->exists()) {
                    $validator->errors()->add('class_room_id', 'You can only announce to classes assigned to you.');
                }
            },
        ];
    }
}
