<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttributeOption extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id','value','status'];
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }


    public function skus_options(): BelongsToMany     {
        return $this->belongsToMany(Sku::class,'attribute_option_sku');
    }

    public function values():HasOne
    {
        return $this->hasOne(AttributeValues::class,'attribute_option_id','id');

    }
    public function orderDetails()
    {
        return $this->belongsToMany(Order_Details::class, 'attribute_order_detail');
    }


}
