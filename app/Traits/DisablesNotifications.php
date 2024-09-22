<?php
namespace App\Traits;

use Filament\Notifications\Notification;

trait DisablesNotifications
{
    protected function notify(Notification $notification): void
    {
        // Override to do nothing, effectively disabling notifications
    }
}
