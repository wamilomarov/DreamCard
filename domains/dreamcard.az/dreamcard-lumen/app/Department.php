<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:16
 */

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;
    protected $fillable = ['name', 'username', 'first_entry'];

    protected $hidden = ['partner_id', 'photo_id', 'city_id', 'password', 'api_token'];

    protected $dates = ['deleted_at'];

    protected $appends = ['partner', 'photo', 'city'];

    public function getPartnerAttribute()
    {
        return Partner::find($this->partner_id);
    }

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }

    public function getCityAttribute()
    {
        return DB::table('cities')->where('id', $this->city_id)->first();
    }

    public function deletePhoto()
    {
        $photo = Photo::find($this->photo_id);
        return $photo->remove('uploads/photos/departments/');
    }

    public function scopeArrangeUser($query)
    {
        if (Auth::user()->getTable() == 'admins' || Auth::user()->getTable() == 'partners')
        {
            return $query->withTrashed();
        }
        else
        {
            return $query;
        }
    }

    public function isEditableByGuard()
    {
        switch (Auth::user()->getTable())
        {
            case "admins" : return true;
            case "partners" : return $this->partner->id == Auth::user()->id ?  true : false;
            case "department" : return $this->id == Auth::user()->id ?  true : false;
            default : return false;
        }
    }


}