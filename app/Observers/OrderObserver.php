<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
//    public function updated(Order $order): void
//    {
//        if ($order->isDirty('order__status_id')) {
//            $order->status_changed_by = Auth::id();
//            $order->status_changed_at = now();
//        }
//    }
    public function updating(Order $order)
    {
        if ($order->isDirty('order__status_id')) {
            $order->status_changed_by = Auth::id();
            $order->status_changed_at = now();
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
