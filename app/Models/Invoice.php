<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $service_order_id
 * @property int $total_biaya
 * @property PaymentStatus $status_bayar
 * @property PaymentMethod|null $metode_bayar
 * @property-read ServiceOrder $serviceOrder
 * @property-read Collection<int, InvoiceItem> $items
 */
#[Fillable(['service_order_id', 'total_biaya', 'status_bayar', 'metode_bayar'])]
class Invoice extends Model
{
    /**
     * @return BelongsTo<ServiceOrder, $this>
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * @return HasMany<InvoiceItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isLunas(): bool
    {
        return $this->status_bayar === PaymentStatus::Lunas;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_bayar' => PaymentStatus::class,
            'metode_bayar' => PaymentMethod::class,
            'total_biaya' => 'integer',
        ];
    }
}
