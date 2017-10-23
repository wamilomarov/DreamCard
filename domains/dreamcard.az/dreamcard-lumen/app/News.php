<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:32
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class News extends Model
{

    protected $table = 'news';

    protected $fillable = ['title', 'content'];

    protected $appends = ['photo', 'category'];

    protected $hidden = ['category_id', 'photo_id'];

    public function photo()
    {
        return $this->hasOne('App\Photo');
    }

    public function category()
    {
        return $this->hasOne('App\Category');
    }

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }

    public function getCategoryAttribute()
    {
        return Category::find($this->category_id);
    }

}