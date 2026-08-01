<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('plate_number')) {
            $this->merge([
                'plate_number' => strtoupper(trim($this->plate_number)),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $vehicleId = $this->route('vehicle') ? $this->route('vehicle')->id : null;

        return [
            'plate_number' => [
                'required', 'string', 'min:3', 'max:20',
                \Illuminate\Validation\Rule::unique('vehicles', 'plate_number')->ignore($vehicleId)
            ],
            'name' => 'required|string|min:2|max:100',
            'fuel_price_per_liter' => 'required|numeric|min:1',
            'km_per_liter' => 'required|numeric|min:0.1',
        ];
    }
}
