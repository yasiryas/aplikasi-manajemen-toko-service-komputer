<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $nama
 * @property string $no_hp
 * @property string|null $alamat
 * @property-read Collection<int, Device> $devices
 */
#[Fillable(['nama', 'no_hp', 'alamat'])]
class Customer extends Model
{
    /**
     * @return HasMany<Device, $this>
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * @return HasManyThrough<ServiceOrder, Device, $this>
     */
    public function serviceOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            ServiceOrder::class,
            Device::class,
            'customer_id',
            'device_id',
            'id',
            'id',
        );
    }
}
