<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:31
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Package extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'price', 'discount_price', 'duration', ];

    protected $dates = ['deleted_at'];

    public function scopeArrangeUser($query)
    {
        if (Auth::user() != null && Auth::user()->getTable() == 'admins')
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
        if (Auth::user()) {
            switch (Auth::user()->getTable()) {
                case "admins" :
                    return true;
                case "partners" :
                    return $this->partner->id == Auth::user()->id ? true : false;
                case "department" :
                    return $this->id == Auth::user()->id ? true : false;
                default :
                    return false;
            }
        }
        else
        {
            return false;
        }
    }
}