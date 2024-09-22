<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_Status extends Model
{
    use HasFactory;
    protected $fillable=['status_name','status_color'];

    public function orders()
    {
        return $this->hasMany(Order::class,'order__status_id');

    }

}
