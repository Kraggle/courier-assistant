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
            'name' => ['nullable', 'string', 'max:55', 'required_without:note'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2222', 'required_with:name'],
            'address' => ['nullable', 'string', 'required_with:name'],
            'note' => ['nullable', 'string', 'required_without:name'],
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
            'lng.required' => __('Something went wrong. Please close the dialog and try again.'),
        ];
    }
}
