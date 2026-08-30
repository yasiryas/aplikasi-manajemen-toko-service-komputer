<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case WhatsApp = 'whatsapp';
    case Sms = 'sms';
    case Email = 'email';
}
