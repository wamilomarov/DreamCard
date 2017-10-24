<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:25
 */

namespace App;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

class Photo extends Model
{
    protected $fillable = ['id', 'url'];

    protected $hidden = ['created_at', 'updated_at'];

    public function news()
    {
        return $this->belongsTo('App\News');
    }

    function scale_image($src_image, $mime, $dst_width, $dst_height, $op = 'fit') {
        $src_width = imagesx($src_image);
        $src_height = imagesy($src_image);


        $dst_image = imagecreatetruecolor($dst_width, $dst_height);

        // Try to match destination image by width
        $new_width = $dst_width;
        $new_height = round($new_width*($src_height/$src_width));
        $new_x = 0;
        $new_y = round(($dst_height-$new_height)/2);

        // FILL and FIT mode are mutually exclusive
        if ($op =='fill')
            $next = $new_height < $dst_height; else $next = $new_height > $dst_height;

        // If match by width failed and destination image does not fit, try by height
        if ($next) {
            $new_height = $dst_height;
            $new_width = round($new_height*($src_width/$src_height));
            $new_x = round(($dst_width - $new_width)/2);
            $new_y = 0;
        }

        // Copy image on right place
        imagecopyresampled($dst_image, $src_image , $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);

//        if ($mime == 'image/png')
//        {
//            return imagepng($dst_image);
//        }
//        else
//        {
//            return imagejpeg($dst_image);
//        }
        return $dst_image;

    }

    public function upload(UploadedFile $file, $folder, $width, $height)
    {


        if (in_array($file->getMimeType(), ['image/jpg', 'image/jpeg', 'image/png']))
        {


            $width == null ? $width = 500: '';
            $height == null ? $height = 500: '';
            $name = md5(uniqid());
            $extension = $file->extension();
            $name = $name . '.' . $extension;
//            $file = $this->scale_image($src_image, $file->getMimeType(), $width, $height);

            $file->move($folder, $name);
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