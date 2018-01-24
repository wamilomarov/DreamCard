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
        $partner = Partner::arrangeUser()->find($this->partner_id);
        if ($partner != null)
        {
            return Category::arrangeUser()->find($partner->category_id);
        }
        else
        {
            return $partner;
        }

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
        return $query->has('partner')->has('partner.category');
      }
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }



}