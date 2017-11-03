<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:21
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['number', 'qr_code', 'qr_created_at', 'balance'];

    protected $hidden = ['user_id', 'photo_id'];

    protected $appends = ['user', 'photo'];

    public function getUserAttribute()
    {
        return User::find($this->user_id);
    }

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }

    public function deletePhoto()
    {
        $photo = Photo::find($this->photo_id);
        return $photo->remove('uploads/photos/cards/');
    }

    public function generateQrCode()
    {
        $qr_code = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        if (Card::where('qr_code', $qr_code)->exists())
        {
            return $this->generateQrCode();
        }
        $this->qr_code = $qr_code;
        $this->qr_created_at = Date('Y-m-d H:i:s');
        return $qr_code;
    }

    public function generateNumber()
    {
        $first_part = mt_rand(10, 100);
        $second_part = mt_rand(10, 100);
        $third_part = mt_rand(10, 100);

        $number = $first_part*10000 + $second_part*100 + $third_part;

        if (Card::where('number', $number)->exists())
        {
            return $this->generateNumber();
        }

        $this->number = $number;
        return $number;
    }
}