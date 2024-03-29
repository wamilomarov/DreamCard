<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 00:07
 */

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Partner extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'username', 'first_entry'];

    protected $hidden = ['category_id', 'photo_id', 'password', 'api_token'];

    protected $dates = ['deleted_at'];

    protected $appends = ['category', 'photo', 'rating', 'is_favorite', 'my_rate', 'campaigns_count'];

    public function getCampaignsCountAttribute()
    {
        if (Auth::user() != null && Auth::user()->getTable() == 'user')
        {
            return Campaign::where('partner_id', $this->id)->where('end_date', '>', Date('Y-m-d H:i:s'))->count();
        }
        else
        {
            return Campaign::where('partner_id', $this->id)->count();
        }

    }

    public function getRatingAttribute()
    {
        $rating = DB::table("ratings")->where("partner_id", $this->id)->avg("rate");
        return $rating == null ? 0 : $rating;
    }

    public function getIsFavoriteAttribute()
    {
        if (Auth::user())
        {
            return DB::table('favorites')->where(['user_id' => Auth::user()->id, 'partner_id' => $this->id])->count();
        }
        else
        {
            return 0;
        }
    }

    public function getMyRateAttribute()
    {
        if (Auth::user())
        {
            $rating = DB::table("ratings")->where(['user_id' => Auth::user()->id, 'partner_id' => $this->id])->select('rate')->first();
            return $rating == null ? 0 : $rating->rate;
        }
        else
        {
            return 0;
        }

    }

    public function getCategoryAttribute()
    {
        return Category::arrangeUser()->find($this->category_id);
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
        return $photo->remove('uploads/photos/partners/');
    }

    public function scopeArrangeUser($query)
    {
        if (Auth::user() != null && Auth::user()->getTable() == 'admins')
        {
            return $query->withTrashed();
        }
        else
        {
            return $query->has('category');
        }
    }

    public function news()
    {
        return $this->hasMany(News::class)->orderByDesc("created_at");
    }

    public function lastNews()
    {
        return $this->hasOne(News::class)->latest();
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class)->orderByDesc("created_at")->where("end_date", ">", DB::raw("NOW()"));
    }

    public function campaign()
    {
        return $this->hasOne(Campaign::class)->where("end_date", ">", DB::raw("NOW()"))->latest();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isEditableByGuard()
    {
        if (Auth::user()) {
            switch (Auth::user()->getTable()) {
                case "admins" :
                    return true;
                case "partners" :
                    return $this->partner->id == Auth::user()->id ? true : false;
                case "department" :
                    return $this->id == Auth::user()->id ? true : false;
                default :
                    return false;
            }
        }
        else
        {
            return false;
        }
    }


}