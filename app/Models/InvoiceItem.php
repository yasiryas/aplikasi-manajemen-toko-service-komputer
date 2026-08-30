<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $invoice_id
 * @property string $nama_item
 * @property string $tipe
 * @property int $qty
 * @property string $harga
 */
#[Fillable(['invoice_id', 'nama_item', 'tipe', 'qty', 'harga'])]
class InvoiceItem extends Model
{
    public $timestamps = false;

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function subtotal(): float
    {
        return $this->qty * (float) $this->harga;
    }
}
