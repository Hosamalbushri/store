<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValues extends Model
{
    use HasFactory;
    protected $fillable=['attribute_option_id','value'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class);
    }

}
