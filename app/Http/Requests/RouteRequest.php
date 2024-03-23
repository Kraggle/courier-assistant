<?php

namespace App\Http\Requests;

use App\Helpers\K;
use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'date' => [
                'required',
                'date',
                'after_or_equal:' . K::user()->firstDSP()->pivot->date
            ],
            'start_time' => ['required'],
            'end_time' => ['nullable', 'after:start_time'],
            'type' => ['required', 'string'],
            'depot_id' => ['required', 'exists:depots,id'],
            'ttfs' => ['integer', 'max:120'],
            'stops' => ['nullable', 'integer', 'min:0'],
            'start_mileage' => ['nullable', 'integer', 'required_unless:type,poc', 'min:0'],
            'end_mileage' => ['nullable', 'integer', 'gt:start_mileage'],
            'invoice_mileage' => ['nullable', 'integer', 'min:0'],
            'bonus' => ['nullable', 'decimal:0,2'],
        ];
    }

    public function prepareForValidation() {
        $vat = $this->input('vat') ? 1 : 0;

        $start_mileage = $this->input('start_mileage', 0) + $this->input('start_mileage_plus', 0);
        $end_mileage = $this->input('end_mileage', 0) + $this->input('end_mileage_plus', 0);
        $this->merge([
            'start_mileage' => $start_mileage == 0 ? null : $start_mileage,
            'end_mileage' => $end_mileage == 0 ? null : $end_mileage,
            'vat' => $vat
        ]);
    }

    public function messages(): array {
        return [
            'date.after_or_equal' => __('You can not add a route before your start date with the DSP.')
        ];
    }
}
