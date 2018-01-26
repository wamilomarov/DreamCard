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
        return $this->delete();
    }

    public function test($imagepath, $new_height, $new_width){
        $src = imagecreatefromjpeg($imagepath);
        list($width, $height) = getimagesize($imagepath);
        $tmp = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($tmp, $src, 0,0,0,0,$new_width, $new_height, $width,$height);
        imagejpeg($tmp, "uploads/done.jpg", 100);
        imagedestroy($src);
        imagedestroy($tmp);
    }

    public function resize($mimeType, $new_width, $new_height, $destination)
    {
        $imagepath = $this->url;
        $src = $mimeType == 'image/png' ? imagecreatefrompng($imagepath) : imagecreatefromjpeg($imagepath);
        list($width, $height) = getimagesize($imagepath);
        $tmp = imagecreatetruecolor(100, 100);
        imagealphablending($tmp, false);
        imagesavealpha($tmp,true);
        $transparency = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
        imagefilledrectangle($tmp, 0, 0, $new_width, $new_height, $transparency);
        imagecopyresampled($tmp, $src, 0,0,0,0,$new_width, $new_height, $width,$height);
        $name = md5(uniqid());
        imagepng($tmp, "$destination"."$name.png", 3);
        imagedestroy($src);
        imagedestroy($tmp);
        return "$destination$name.png";
    }

}