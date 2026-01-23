<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['required', 'array', 'min:1'],
            'translations.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'translations.required' => 'Необходимо указать хотя бы один перевод',
            'translations.*.required' => 'Название перевода обязательно для заполнения',
            'translations.*.max' => 'Название перевода не должно превышать 255 символов',
        ];
    }
}
