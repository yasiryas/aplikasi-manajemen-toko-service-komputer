<?php

use App\Models\ServiceLog;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    ServiceLog::query()
        ->where('created_at', '<', now()->subMonths(6))
        ->delete();
})->daily()->description('Prune service logs older than six months');
