<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer_Address extends Model
{
    use HasFactory;

    protected $fillable=['customer_id','firstname','lastname','phone','address','city','country','neighborhood'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }


//    public function getFirstNameAttribute($value)
//    {
//
//        // Check if the current request is coming from the Filament panel
//        if (str_contains(request()->path(), 'admin')) {
//            return $value. ' '.$this->lastname;
//        }
//        return ucfirst($value);  // Apply transformation for other requests
//
//
//    }
}
