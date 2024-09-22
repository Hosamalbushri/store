<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;
    protected $table='carts';
    protected $fillable=['customer_id'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);

    }

    public function cart_detailes()
    {
        return $this->hasMany(Cart_Detailes::class)
            ->where('cart_id', $this->id);
    }
}
