<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // izinkan semua user yang sudah login
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'in:pending,done',
            'due_date'    => 'nullable|date|after_or_equal:today',
            'is_public'   => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Judul task wajib diisi.',
            'due_date.after_or_equal' => 'Due date tidak boleh di masa lalu.',
        ];
    }
}