<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePickupTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        // Asumsi struktur role ada direlasi, sesuaikan jika berbeda
        $roleName = $user->role->name ?? '';
        return in_array(strtolower($roleName), ['super_admin', 'operator', 'admin']);
    }

    public function rules(): array
    {
        return [
            'reference_number' => 'nullable|string|max:255',
            'transaction_source' => 'nullable|in:purchase,manual',
            'driver_id' => 'required|uuid|exists:users,id',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'pickup_name' => 'required|string|max:255',
            'pickup_location' => 'required|string',
            'destination' => 'nullable|string|max:255',
            'item_number' => 'nullable|string|max:255',
            'item_description' => 'required|string',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
        ];
    }
}
