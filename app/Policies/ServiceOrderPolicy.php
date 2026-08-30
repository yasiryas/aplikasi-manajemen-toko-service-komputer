<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ServiceOrder;
use App\Models\User;

class ServiceOrderPolicy
{
    public function view(User $user, ServiceOrder $order): bool
    {
        return $user->isAdmin() || $this->assignedTo($user, $order);
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, ServiceOrder $order): bool
    {
        return $user->isStaff() && ($user->isAdmin() || $this->assignedTo($user, $order));
    }

    public function changeStatus(User $user, ServiceOrder $order): bool
    {
        return $user->isStaff() && ($user->isAdmin() || $this->assignedTo($user, $order));
    }

    public function notify(User $user, ServiceOrder $order): bool
    {
        return $user->isStaff() && ($user->isAdmin() || $this->assignedTo($user, $order));
    }

    public function delete(User $user, ServiceOrder $order): bool
    {
        return $user->role === UserRole::Admin;
    }

    private function assignedTo(User $user, ServiceOrder $order): bool
    {
        return $order->teknisi_id === $user->id || $order->teknisi_id === null;
    }
}
