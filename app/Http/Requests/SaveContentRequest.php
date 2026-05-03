<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contentId = $this->route('content')?->id;

        return [
            'type' => ['required', 'string', 'max:60'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled', 'private'])],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:190'],
            'body' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', Rule::exists('contents', 'id')->whereNull('deleted_at')],
            'featured_media_id' => ['nullable', 'integer'],
            'template' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', Rule::exists('taxonomies', 'id')],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', Rule::exists('taxonomies', 'id')],
        ];
    }
}
