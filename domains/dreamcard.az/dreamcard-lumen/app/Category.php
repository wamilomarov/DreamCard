<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 22-Oct-17
 * Time: 23:56
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    protected $hidden = ['small_icon_id', 'large_icon_id', 'order_by'];

    protected $dates = ['deleted_at'];

    protected $appends = ['small_icon', 'large_icon', 'partners_count'];

    public function getPartnersCountAttribute()
    {
        return DB::table('partners')->where('category_id', $this->id)->count();
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
        return $photo->remove('uploads/photos/categories/');
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