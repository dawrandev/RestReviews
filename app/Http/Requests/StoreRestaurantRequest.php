<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'branch_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['sometimes', 'image', 'max:5120'],
            'operating_hours' => ['nullable', 'array'],
            'operating_hours.*.opening_time' => ['nullable', 'date_format:H:i'],
            'operating_hours.*.closing_time' => ['nullable', 'date_format:H:i'],
            'operating_hours.*.is_closed' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand_id.required' => 'Выберите бренд',
            'brand_id.exists' => 'Выбранный бренд не существует',
            'city_id.required' => 'Выберите город',
            'city_id.exists' => 'Выбранный город не существует',
            'branch_name.required' => 'Название филиала обязательно',
            'address.required' => 'Адрес обязателен',
            'latitude.required' => 'Укажите локацию на карте',
            'longitude.required' => 'Укажите локацию на карте',
            'images.max' => 'Максимум 5 фотографий',
            'images.*.required' => 'Файл не был загружен правильно',
            'images.*.image' => 'Файл должен быть изображением (JPG, PNG)',
            'images.*.max' => 'Размер изображения не должен превышать 5MB',
        ];
    }
}
