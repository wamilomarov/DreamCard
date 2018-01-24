<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Jan-18
 * Time: 01:22
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Purchase extends Model
{

    protected $fillable = ['amount', 'discount', 'created_at'];

    protected $hidden = ['card_id', 'partner_id', 'campaign_id'];

    protected $appends = [];


    public function partner()
    {
        return $this->belongsTo('App\Partner');
    }

}