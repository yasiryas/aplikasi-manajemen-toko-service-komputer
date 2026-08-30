<?php

namespace App\Http\Requests;

use App\Enums\ServiceOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'status' => ['required', Rule::enum(ServiceOrderStatus::class)],
            'teknisi_id' => ['nullable', 'integer', 'exists:users,id'],
            'estimasi_biaya' => ['nullable', 'numeric', 'min:0'],
            'tanggal_masuk' => ['required', 'date'],
        ];
    }
}
