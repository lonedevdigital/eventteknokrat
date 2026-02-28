<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'nullable|string',
            'registration_start' => 'sometimes|date',
            'registration_end' => 'sometimes|date|after_or_equal:registration_start',
            'event_date' => 'sometimes|date|after:registration_end',
            'location' => 'nullable|string|max:255',
        ];
    }
}
