<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:07
 */

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Partner extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'username', 'first_entry'];

    protected $hidden = ['category_id', 'photo_id', 'password', 'api_token'];

    protected $dates = ['deleted_at'];

    protected $appends = ['category', 'photo'];

    public function getCategoryAttribute()
    {
        return Category::find($this->category_id);
    }

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }

    public function deletePhoto()
    {
        $photo = Photo::find($this->photo_id);
        return $photo->remove('uploads/photos/partners/');
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

}