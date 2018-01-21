<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:32
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class News extends Model
{

    protected $table = 'news';

    protected $fillable = ['title', 'content'];

    protected $appends = ['photo', 'category'];

    protected $hidden = ['partner_id', 'photo_id'];

    public function photo()
    {
        return $this->hasOne('App\Photo');
    }

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }

    public function getCategoryAttribute()
    {
        return Category::find(Partner::find($this->partner_id)->category_id);
    }

    public function deletePhoto()
    {
        $photo = Photo::find($this->photo_id);
        if($photo == null)
        {
            return true;
        }
        return $photo->remove('uploads/photos/news/');
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

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }



}