<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'phone', 'first_name', 'last_name', 'get_news'
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
        return Card::where('user_id', $this->id)->first();
    }

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }

    public function deletePhoto()
    {
        $photo = Photo::find($this->photo_id);
        return $photo->remove('uploads/photos/users/');
    }

    public function scopeArrangeUser($query)
    {
        if (Auth::user()->getTable() == 'admins')
        {
            return $query->withTrashed();
        }
        else
        {
            return $query;
        }
    }

    public function favoritePartners($category_id)
    {
        return $this->belongsToMany("App\Partner", "favorites" )->where("category_id", "=", $category_id);
    }

    public function favoriteCategories()
    {
        return $this->favoritePartners()->groupBy("partners.category_id");
    }

}
