<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePluginSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
        ];
    }
}
