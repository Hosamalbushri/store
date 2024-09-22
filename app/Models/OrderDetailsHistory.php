<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetailsHistory extends Model
{
    use HasFactory;
    protected $table = 'order_details_history';

    protected $fillable = ['order_id', 'details_data'];
}
