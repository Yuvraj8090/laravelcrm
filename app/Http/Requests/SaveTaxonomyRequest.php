<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTaxonomyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['category', 'tag', 'custom'])],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', Rule::exists('taxonomies', 'id')],
        ];
    }
}
