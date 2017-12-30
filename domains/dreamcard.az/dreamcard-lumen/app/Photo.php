<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:25
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Photo extends Model
{
    protected $fillable = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return "http://dreamcard.az/api/" . $this->attributes['url'];
    }

    public function news()
    {
        return $this->belongsTo('App\News');
    }

    public function upload(UploadedFile $file, $folder)
    {

        if (in_array($file->getMimeType(), ['image/jpg', 'image/jpeg', 'image/png']))
        {

            $name = md5(uniqid());
            $extension = $file->extension();
            $name = $name . '.' . $extension;

            $file->move($folder, $name);
            $this->url = $folder . $name;
            $this->save();
            return 200;
        }
        else
        {
            return 405;
        }
    }

    public function remove($folder = null)
    {
        unlink($this->attributes['url']);
        return $this->forceDelete();
    }
}