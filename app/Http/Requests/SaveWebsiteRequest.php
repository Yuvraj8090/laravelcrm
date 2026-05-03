<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $websiteId = $this->route('website')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'alpha_dash', 'max:150', Rule::unique('websites', 'slug')->ignore($websiteId)],
            'status' => ['required', Rule::in(['active', 'inactive', 'maintenance'])],
            'primary_domain' => ['nullable', 'string', 'max:255', Rule::unique('websites', 'primary_domain')->ignore($websiteId)],
            'theme_slug' => ['nullable', 'string', 'max:120'],
            'locale' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'string', 'max:64'],
        ];
    }
}
