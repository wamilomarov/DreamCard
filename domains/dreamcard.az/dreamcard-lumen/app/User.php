<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'phone', 'first_name', 'last_name', 'get_news'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'firebase_id', 'photo_id', 'city_id', 'facebook_id', 'google_id', 'api_token', 'status'
    ];

    protected $appends = ['city', 'photo', 'card'];

    public function photo()
    {
        return $this->hasOne('App\Photo');
    }

    public function card()
    {
        return $this->belongsTo('App\Card');
    }

    public function getCityAttribute()
    {
        return DB::table('cities')->where('id', $this->city_id)->first();
    }

    public function getCardAttribute()
    {
        return Card::find($this->card_id);
    }

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }
}
