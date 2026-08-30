<?php

namespace App\Models;

use App\Enums\ServiceOrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $device_id
 * @property string $no_tiket
 * @property ServiceOrderStatus $status
 * @property int|null $teknisi_id
 * @property int|null $estimasi_biaya
 * @property Carbon $tanggal_masuk
 * @property Carbon|null $tanggal_selesai
 * @property-read Device $device
 * @property-read User|null $teknisi
 * @property-read Invoice|null $invoice
 * @property-read Collection<int, ServiceLog> $logs
 */
#[Fillable(['device_id', 'no_tiket', 'status', 'teknisi_id', 'estimasi_biaya', 'tanggal_masuk', 'tanggal_selesai'])]
class ServiceOrder extends Model
{
    /**
     * @return BelongsTo<Device, $this>
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    /**
     * @return HasOne<Invoice, $this>
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * @return HasMany<ServiceLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ServiceLog::class);
    }

    /**
     * @return HasMany<NotificationLog, $this>
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function hasInvoice(): bool
    {
        return $this->invoice()->exists();
    }

    public static function generateTicketNumber(): string
    {
        $max = self::query()->max('no_tiket');
        $sequence = $max ? ((int) substr($max, 3)) + 1 : 1;

        return 'TS-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ServiceOrderStatus::class,
            'estimasi_biaya' => 'integer',
            'tanggal_masuk' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }
}
