<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Sku extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','code','price','quantity'];

    protected function price(): Attribute
    {
        return Attribute::make(get: static fn($value) => $value / 100,
            set: static fn($value) => $value * 100, );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    public static function boot()
    {
        parent::boot();

        static::saved(function ($sku) {
            $sku->product->save();
        });

        static::deleted(function ($sku) {
            $sku->product->save();
        });
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            do {
                $code = self::generateUniqueNumericCode();
            } while (self::where('code', $code)->exists());

            $model->code = $code;
        });
    }

    private static function generateUniqueNumericCode($length = 10)
    {
        $numbers = '0123456789';
        $code = Str::random(2) ;
        for ($i = 0; $i < $length; $i++) {
            $code .= $numbers[rand(0, strlen($numbers) - 1)].Str::random(1);
        }
        return $code;
    }


    public function attribute_Options(): BelongsToMany     {
        return $this->belongsToMany(AttributeOption::class,'attribute_option_sku');
    }



    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class);
    }


    public function attribute_value(): HasManyThrough
    {
        return $this->hasManyThrough(Attribute::class,AttributeOption::class);
    }


    public function saveFiltered(array $data): bool
    {
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return $this->fill($filteredData)->save();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order_Details::class);
    }

    public function updateQuantity($quantityChange)
    {
        $this->quantity = $this->quantity + $quantityChange;
        $this->save();
    }

}
