<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkContentActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content_ids' => ['required', 'array', 'min:1'],
            'content_ids.*' => ['integer', Rule::exists('contents', 'id')],
            'action' => ['required', Rule::in(['publish', 'draft', 'delete', 'restore', 'category'])],
            'taxonomy_id' => ['nullable', 'integer', Rule::exists('taxonomies', 'id')],
        ];
    }
}
