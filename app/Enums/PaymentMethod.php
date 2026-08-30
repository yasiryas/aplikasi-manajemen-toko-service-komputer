<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Tunai = 'tunai';
    case Transfer = 'transfer';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
