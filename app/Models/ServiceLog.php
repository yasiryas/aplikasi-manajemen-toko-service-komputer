<?php

namespace App\Models;

use App\Enums\ServiceOrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $service_order_id
 * @property ServiceOrderStatus|string $status
 * @property string|null $catatan
 * @property int $changed_by
 * @property Carbon|null $created_at
 */
#[Fillable(['service_order_id', 'status', 'catatan', 'changed_by'])]
class ServiceLog extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(fn (ServiceLog $log) => $log->created_at = Carbon::now());
    }

    /**
     * @return BelongsTo<ServiceOrder, $this>
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
