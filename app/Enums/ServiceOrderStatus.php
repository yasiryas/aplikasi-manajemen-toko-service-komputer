<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case Antri = 'antri';
    case Dikerjakan = 'dikerjakan';
    case MenungguSparepart = 'menunggu_sparepart';
    case Selesai = 'selesai';
    case Diambil = 'diambil';

    public function label(): string
    {
        return match ($this) {
            self::Antri => 'Antri',
            self::Dikerjakan => 'Dikerjakan',
            self::MenungguSparepart => 'Menunggu Sparepart',
            self::Selesai => 'Selesai',
            self::Diambil => 'Diambil',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Antri => 'bg-amber-100 text-amber-700',
            self::Dikerjakan => 'bg-indigo-100 text-indigo-700',
            self::MenungguSparepart => 'bg-rose-100 text-rose-700',
            self::Selesai, self::Diambil => 'bg-emerald-100 text-emerald-700',
        };
    }

    public function progressClass(): string
    {
        return match ($this) {
            self::Antri => 'bg-amber-500',
            self::Dikerjakan => 'bg-indigo-500',
            self::MenungguSparepart => 'bg-rose-500',
            self::Selesai, self::Diambil => 'bg-emerald-500',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function active(): array
    {
        return [self::Antri, self::Dikerjakan, self::MenungguSparepart];
    }
}
