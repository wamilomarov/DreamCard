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
use Image;

class Photo extends Model
{
    protected $fillable = ['id', 'url'];

    protected $hidden = ['created_at', 'updated_at'];

    public function news()
    {
        return $this->belongsTo('App\News');
    }

    public function upload(UploadedFile $file, $folder, $width, $height)
    {
        $photo = Image::make($file);

        if (in_array($photo->mime(), ['image/jpg', 'image/jpeg', 'image/png']))
        {
            $width == null ? $width = 500: '';
            $height == null ? $height = 500: '';
            $name = md5(uniqid());
            $extension = $file->extension();
            $name = $name . '.' . $extension;
            $photo->fit($width, $height, function ($constraint) {
                $constraint->upsize();
            })->save($folder . $name);
            $this->url = $name;
            $this->save();
            return 200;
        }
        else
        {
            return 405;
        }


    }
}