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

    protected $hidden = ['partner_id', 'department_id', 'created_at', 'updated_at', 'photo_id'];

    protected $dates = ['deleted_at'];

    protected $appends = ['photo'];

    public function getPhotoAttribute()
    {
        return Photo::find($this->photo_id);
    }

    public function deletePhoto()
    {
        $photo = Photo::find($this->photo_id);
        if ($photo == null)
        {
            return true;
        }
        return $photo->remove('uploads/photos/campaigns/');
    }

//    public function getPartnerAttribute(){
//      return Partner::arrangeUser()->find($this->partner_id);
//    }

//    public function getDepartmentAttribute()
//    {
//      return Department::arrangeUser()->find($this->department_id);
//    }

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

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

}