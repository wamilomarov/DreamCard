<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 26-Oct-17
 * Time: 02:02
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [''];

    protected $hidden = [];

    protected $dates = ['deleted_at'];
}