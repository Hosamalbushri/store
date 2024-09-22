<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;

    use HasFactory;


    protected $fillable=['name','status'];

    public function Subcategoreies():HasMany
    {
        return $this->hasMany(SubCategory::class,'category_id','id');

    }
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(640)
            ->sharpen(10);
    }
}
