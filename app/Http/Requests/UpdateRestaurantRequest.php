<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['sometimes', 'exists:brands,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'branch_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['sometimes', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'new_images' => ['nullable', 'array', 'max:10'],
            'new_images.*' => ['required', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand_id.exists' => 'Выбранный бренд не существует',
            'city_id.exists' => 'Выбранный город не существует',
            'new_images.*.image' => 'Файл должен быть изображением',
            'new_images.*.max' => 'Размер изображения не должен превышать 5MB',
        ];
    }
}
