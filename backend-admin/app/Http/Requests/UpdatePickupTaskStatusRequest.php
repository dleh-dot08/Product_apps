<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePickupTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorisasi ditangani di dalam controller
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:on_route,arrived,delivered,failed,cancelled',
            'failure_reason' => 'required_if:status,failed|nullable|string',
            'proof_photo' => 'required_if:status,delivered|nullable', // Bisa diisi URL string atau file upload
            'completed_odometer' => 'required_if:status,delivered|nullable|integer|min:0',
        ];
    }
}
