<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'device_id.required' => 'Qurilma identifikatori majburiy.',
            'device_id.string' => 'Qurilma identifikatori to\'g\'ri formatda emas.',
            'device_id.max' => 'Qurilma identifikatori juda uzun.',
            'rating.required' => 'Reytingni tanlash majburiy.',
            'rating.integer' => 'Reyting butun son bo\'lishi kerak.',
            'rating.min' => 'Reyting kamida 1 bo\'lishi kerak.',
            'rating.max' => 'Reyting ko\'pi bilan 5 bo\'lishi kerak.',
            'comment.max' => 'Izoh 1000 belgidan oshmasligi kerak.',
        ];
    }
}
