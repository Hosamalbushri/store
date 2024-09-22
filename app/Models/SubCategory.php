<?php

namespace App\Models;

use App\Observers\SubCategoryObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SubCategory extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable=['name','category_id','status'];


    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products():HasMany
    {
        return $this->hasMany(Product::class,'sub_category_id','id');

    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(640)
            ->sharpen(10);
    }






}
