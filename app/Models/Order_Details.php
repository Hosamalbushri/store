<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_Details extends Model
{
    use HasFactory;
    protected $fillable=['order_id','sku_id','quantity','price','discount','total_price'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
    public function attributes()
    {
        return $this->belongsToMany(AttributeOption::class, 'attribute_order_detail');
    }
}

