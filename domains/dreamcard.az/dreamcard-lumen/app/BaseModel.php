<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 17-Jan-18
 * Time: 16:26
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
    public $language;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->language = Auth::user()->language != null ? Auth::user()->language : 'az';
    }
}