<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'class_room_id' => ['nullable', 'integer', 'exists:class_rooms,id'],
            'audience' => ['required', 'in:all,students,parents,teachers'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
