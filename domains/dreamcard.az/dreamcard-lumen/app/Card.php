<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:21
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Card extends Model
{
    protected $fillable = ['number', 'qr_code', 'qr_created_at', 'balance'];

    protected $hidden = ['user_id', 'photo_id'];

    protected $appends = ['valid_for', 'end_date'];

    public function getValidForAttribute()
    {
        if ($this->qr_code == null)
        {
            return 0;
        }
        else
        {
            $diff = 120 - (strtotime(date('Y-m-d H:i:s')) - strtotime($this->qr_created_at));
            return $diff > 0 ? $diff : 0;
        }
    }

    public function getEndDateAttribute()
    {
        $date = DB::table('card_upgrades')->where('card_id', $this->id)->select('end_time')->orderByDesc('end_time')->first();
        return $date == null || $date->end_time == null ? Date('Y-m-d H:i:s') : $date->end_time;
    }

    public function deletePhoto()
    {
        $photo = Photo::find($this->photo_id);
        if ($photo == null)
        {
            return true;
        }
        return $photo->remove('uploads/photos/cards/');
    }

    public function generateQrCode()
    {
        $qr_code = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        if (Card::where('qr_code', $qr_code)->exists())
        {
            $this->generateQrCode();
        }
        $this->qr_code = $qr_code;
        $this->qr_created_at = Date('Y-m-d H:i:s');
        $this->save();
    }

    public function generateNumber()
    {
        $first_part = mt_rand(10, 100);
        $second_part = mt_rand(10, 100);
        $third_part = mt_rand(10, 100);

        $number = $first_part*10000 + $second_part*100 + $third_part;

        if (Card::where('number', $number)->exists())
        {
            $this->generateNumber();
        }

        $this->number = $number;
        $this->save();
    }

    public function upgrade()
    {
        
    }
}