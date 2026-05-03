<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'items' => ['nullable', 'array'],
            'items.*.title' => ['required', 'string', 'max:150'],
            'items.*.url' => ['nullable', 'string', 'max:255'],
            'items.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
