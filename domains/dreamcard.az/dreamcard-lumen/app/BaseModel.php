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
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    public $language;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->language = Auth::user()->language != null ? Auth::user()->language : 'az';
    }

    public function sendEmail()
    {

    }

    public function getPasswordResetMail()
    {
        if ($this->email != null)
        {
            $token = base64_encode(str_random(10));
            $email = $this->email;
            DB::table('password_resets')
                ->insert([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()')
                ]);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function passwordReset($token)
    {
        $reset = DB::table('password_resets')->select('email')->where('token', $token)->first();
    }
}