<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 26-Oct-17
 * Time: 02:02
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Campaign extends Model
{
    use SoftDeletes;
    protected $fillable = [''];

    protected $hidden = ['partner_id', 'department_id'];

    protected $dates = ['deleted_at'];

    protected $appends = ['partner', 'department'];

    public function getPartnerAttribute(){
      return Partner::arrangeUser()->find($this->partner_id);
    }

    public function getDepartmentAttribute(){
      return Department::arrangeUser()->find($this->department_id);
    }

    public function scopeArrangeUser($query)
    {
        if (Auth::user()->getTable() != 'users')
        {
            return $query->withTrashed();
        }
        else
        {
            return $query;
        }
    }

}