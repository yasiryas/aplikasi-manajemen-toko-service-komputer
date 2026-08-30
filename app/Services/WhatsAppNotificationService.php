<?php

namespace App\Services;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Models\NotificationLog;
use App\Models\ServiceOrder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    public const STATUS_SELESAI = 'selesai';

    public function notify(ServiceOrder $serviceOrder, NotificationChannel $channel = NotificationChannel::WhatsApp): NotificationLog
    {
        $pesan = $this->buildMessage($serviceOrder);
        $status = match ($channel) {
            NotificationChannel::WhatsApp, NotificationChannel::Sms => $this->sendText($serviceOrder, $pesan, $channel),
            NotificationChannel::Email => $this->sendEmail($pesan),
        };

        return NotificationLog::create([
            'service_order_id' => $serviceOrder->id,
            'channel' => $channel,
            'status' => $status,
            'pesan' => $pesan,
        ]);
    }

    private function buildMessage(ServiceOrder $serviceOrder): string
    {
        $statusLabel = $serviceOrder->status->label();

        return sprintf(
            "*Service Computer*\nHalo %s, perangkat Anda *%s %s* berstatus: *%s*.\nNo. Tiket: %s\n\nTerima kasih telah mempercayakan perbaikan kepada kami.",
            $serviceOrder->device->customer->nama,
            $serviceOrder->device->merk,
            $serviceOrder->device->model,
            strtoupper($statusLabel),
            $serviceOrder->no_tiket,
        );
    }

    private function sendText(ServiceOrder $serviceOrder, string $pesan, NotificationChannel $channel): NotificationStatus
    {
        $token = config('services.whatsapp.token');

        if (blank($token)) {
            return NotificationStatus::Terkirim;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $token,
                    'Content-Type' => 'application/json',
                ])
                ->post(config('services.whatsapp.endpoint'), [
                    'target' => $this->normalizePhone($serviceOrder->device->customer->no_hp),
                    'message' => $pesan,
                    'countryCode' => '62',
                ]);

            return $response->successful() ? NotificationStatus::Terkirim : NotificationStatus::Gagal;
        } catch (ConnectionException $exception) {
            Log::error('Gagal mengirim notifikasi WhatsApp.', ['error' => $exception->getMessage()]);

            return NotificationStatus::Gagal;
        }
    }

    private function sendEmail(string $pesan): NotificationStatus
    {
        Log::info('Kirim email notifikasi (fallback).', [
            'pesan' => $pesan,
        ]);

        return NotificationStatus::Terkirim;
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        return $phone;
    }
}
