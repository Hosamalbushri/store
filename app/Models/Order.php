<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
        'customer_id',
        'order_number',
        'order_date',
        'payment_type_id',
        'customer_address_id',
        'order__status_id',
        'status_changed_by',
        'status_changed_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Get the last order
            $lastOrder = Order::orderBy('order_number', 'desc')->first();

            // Set the order number to the next increment value
            $order->order_number = $lastOrder ? $lastOrder->order_number + 1 : 1;
        });
    }
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);

    }

    public function orderDetail():HasMany
    {
        return $this->hasMany(Order_Details::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);

    }

    public function customer_address()
    {
        return $this->belongsTo(Customer_Address::class);

    }

    public function getTotalPriceAttribute()
    {
        return $this->orderDetail->sum('total_price');
    }
    public function status()
    {
        return $this->belongsTo(Order_Status::class,'order__status_id');
    }
    public function checkSkuQuantities(): void
    {

        $orderDetails = $this->orderDetail;
        foreach ($orderDetails as $detail) {
            $sku = \App\Models\Sku::find($detail->sku_id);
            if ($sku->quantity < $detail->quantity) {
                $product=$sku->product->name ;
                throw new \Exception("الكمية غير متوفرة  $product");
            }
            $sku->updateQuantity(-$detail->quantity);
        }

    }
    public function UpdateSkuQuantities(): void
    {

            $orderDetails = $this->orderDetail;
            if (isset($orderDetails)) {
                foreach ($orderDetails as $detail) {
                    $sku = \App\Models\Sku::find($detail->sku_id);
                    $sku->updateQuantity($detail->quantity);
                }
            }
    }
    public function userWhoChangedStatus()
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }


}
