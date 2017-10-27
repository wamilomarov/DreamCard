<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:16
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'username', 'first_entry'];

    protected $hidden = ['partner_id', 'photo_id', 'city_id', 'password', 'api_token'];

    protected $dates = ['deleted_at'];


}