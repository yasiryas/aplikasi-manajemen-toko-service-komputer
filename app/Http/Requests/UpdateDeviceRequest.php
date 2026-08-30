<?php

namespace App\Http\Requests;

use App\Enums\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
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
            'jenis' => ['required', Rule::enum(DeviceType::class)],
            'merk' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'keluhan' => ['required', 'string'],
        ];
    }
}
