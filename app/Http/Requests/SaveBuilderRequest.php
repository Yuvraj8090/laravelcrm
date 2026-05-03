<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveBuilderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sections' => ['required', 'array'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.name' => ['nullable', 'string', 'max:150'],
            'sections.*.type' => ['required', 'string', 'max:80'],
            'sections.*.sort_order' => ['nullable', 'integer'],
            'sections.*.is_reusable' => ['nullable', 'boolean'],
            'sections.*.settings' => ['nullable', 'array'],
        ];
    }
}
