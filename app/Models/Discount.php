<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Discount extends Model
{
    use HasFactory;
    protected $fillable=['sku_id','value','discounted_price','sku_id'];
    protected $hidden=['created_at','updated_at','id','sku_id'];

    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

}
