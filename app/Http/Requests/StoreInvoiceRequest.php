<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'service_order_id' => ['required', 'integer', 'exists:service_orders,id', Rule::unique('invoices', 'service_order_id')],
            'status_bayar' => ['required', Rule::enum(PaymentStatus::class)],
            'metode_bayar' => ['nullable', Rule::enum(PaymentMethod::class)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama_item' => ['required', 'string', 'max:255'],
            'items.*.tipe' => ['required', Rule::in(['jasa', 'sparepart'])],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga' => ['required', 'numeric', 'min:0'],
        ];
    }
}
