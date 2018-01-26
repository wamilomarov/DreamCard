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

class Campaign extends BaseModel
{
    use SoftDeletes;
    protected $fillable = ['end_date'];

    protected $hidden = ['partner_id', 'created_at', 'updated_at', 'photo_id', 'title_en', 'title_az', 'title_ru'];

    protected $dates = ['deleted_at'];

    protected $appends = ['photo', 'expired', 'title'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (Auth::user()->getTable() == 'admins')
        {
            $this->makeVisible(['title_az', 'title_en', 'title_ru']);
        }
    }

    public function getTitleAttribute()
    {
        $column = "title_$this->language";
        return $this->$column;
    }

    public function getExpiredAttribute()
    {
        return $this->end_date < Date("Y-m-d H:i:s");
    }

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
            return $query->has('partner')->has('partner.category');
        }
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }


}