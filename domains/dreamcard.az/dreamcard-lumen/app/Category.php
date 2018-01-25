<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 22-Oct-17
 * Time: 23:56
 */

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Category extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [];

    protected $hidden = ['small_icon_id', 'large_icon_id', 'order_by', 'name_az', 'name_ru', 'name_en'];

    protected $dates = ['deleted_at'];

    protected $appends = ['small_icon', 'large_icon', 'partners_count', 'name'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (Auth::user()->getTable() == 'admins')
        {
            $this->makeVisible(['name_az', 'name_en', 'name_ru']);
        }
    }

    public function getPartnersCountAttribute()
    {
        return DB::table('partners')->where('category_id', $this->id)->whereNull("deleted_at")->count();
    }

    public function getNameAttribute()
    {
        $column = "name_$this->language";
        return $this->$column;
    }

    public function getSmallIconAttribute()
    {
        return Photo::find($this->small_icon_id);
    }

    public function getLargeIconAttribute()
    {
        return Photo::find($this->large_icon_id);
    }

    public function deleteSmallIcon()
    {
        $photo = Photo::find($this->small_icon_id);
        return $photo->remove();
    }

    public function deleteLargeIcon()
    {
        $photo = Photo::where($this->large_icon_id);
        return $photo->remove();
    }

    public function scopeArrangeUser($query)
    {
        if (Auth::user()->getTable() == 'admins')
        {
            return $query->withTrashed();
        }
        else
        {
            return $query;
        }
    }

}