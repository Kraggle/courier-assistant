<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfoRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'id' => ['nullable', 'integer', 'exists:info,id'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'address' => ['required', 'string'],
            'note' => ['required', 'string'],
        ];
    }

    public function prepareForValidation() {
        $this->merge([
            'position->lat' => $this->lat ?? null,
            'position->lng' => $this->lng ?? null,
        ]);
    }

    public function messages(): array {
        return [
            'lng.required' => __('Something went wrong. Please refresh the page.'),
        ];
    }
}
