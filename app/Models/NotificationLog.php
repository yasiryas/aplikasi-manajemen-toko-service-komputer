<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $service_order_id
 * @property NotificationChannel $channel
 * @property NotificationStatus $status
 * @property string $pesan
 * @property Carbon|null $created_at
 */
#[Fillable(['service_order_id', 'channel', 'status', 'pesan'])]
class NotificationLog extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(fn (NotificationLog $log) => $log->created_at = Carbon::now());
    }

    /**
     * @return BelongsTo<ServiceOrder, $this>
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'status' => NotificationStatus::class,
        ];
    }
}
