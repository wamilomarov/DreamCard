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
    protected $fillable = ['number'];

    protected $hidden = ['user_id', 'photo_id'];
}