<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject,HasMedia
{
    use HasApiTokens, HasFactory, Notifiable  ;
    use InteractsWithMedia;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'birthday',
        'password',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'name' => $this->name,
            'email'=>$this->email,
            'birthday'=>$this->birthday
        ];
    }
    public function orders():HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function addresses():HasMany
    {
        return $this->hasMany(Customer_Address::class);
    }
    public function cart():HasOne
    {
        return $this->hasOne(Cart::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::created(function ($customer) {
            // Create a new cart for the customer
            Cart::create(['customer_id' => $customer->id]);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class);

    }
    public function order_detaile()
    {
        return $this->hasManyThrough(Order_Details::class,Order::class);
    }


}
