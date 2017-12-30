<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 26-Dec-17
 * Time: 00:12
 */

namespace App;


use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authorizable, Authenticatable, SoftDeletes;

    protected $fillable = ['username', 'email', 'phone'];

    protected $hidden = ['password', 'api_token'];



}