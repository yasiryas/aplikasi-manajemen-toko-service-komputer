<?php

namespace App\Enums;

enum DeviceType: string
{
    case Laptop = 'laptop';
    case PC = 'pc';
    case Printer = 'printer';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
