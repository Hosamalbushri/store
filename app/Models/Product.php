<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    protected $fillable=['name','description','status','availability','sub_category_id'];
//    protected $appends=['images'];

    public function scopeActive($query)
    {
        return $query->where('status', true)->where('availability', true);
    }

    public function photo()
    {
        return  ($this ->getMedia()->map(fn($media) => $media->getUrl('thumb'))->toArray());
    }




    public function subcategory():BelongsTo
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id','id');

    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    public function favorite(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function getTotalQuantityAttribute()
    {
        return $this->skus->sum('quantity');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->availability = $model->getTotalQuantityAttribute() > 0 ? 1 : 0;
        });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(640)
            ->sharpen(10)
            ->queued();
    }











}
