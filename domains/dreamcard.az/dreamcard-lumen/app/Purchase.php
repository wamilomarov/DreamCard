<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Jan-18
 * Time: 01:22
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{

    protected $fillable = ['amount', 'discount', 'created_at'];

    protected $hidden = ['card_id', 'department_id', 'campaign_id'];

    protected $appends = ['partner'];

    public function getPartnerAttribute()
    {
        return Partner::find(Department::find($this->department_id)->partner_id);
    }

    public function partner()
    {
        return $this->belongsTo(Department::class)->with('partner');
    }
}