<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 04:00
 */

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{

    public function create(Request $request)
    {
        if ($request->has('username') && $request->has('email')
            && $request->has('phone') && $request->has('password'))
        {
            if (User::where('email', $request->get('email'))
                ->orWhere('username', $request->get('username'))
                ->orWhere('phone', $request->get('phone'))->exists())
            {
                $result = ['status' => 407];
            }
            else
            {
                $user = new User();
                $user->username = $request->get('username');
                $user->email = $request->get('email');
                $user->phone = $request->get('phone');
                $user->password = app('hash')->make($request->get('password'));
                $user->photo_id = NULL;

                $user->save();
                $result = ['status' => 200, 'data' => ['user' => $user]];
            }

        }
        else
        {
            $result = ['status' => 406];
        }

        return response($result);
    }

    public function login(Request $request)
    {
        if ($request->has('username') && $request->has('password'))
        {
            $user = User::where('email', $request->get('email'))
                ->where('status', 3)->first();

            if(Hash::check($request->get('password'), $user->getAuthPassword()))
            {
                $user->api_token = md5(microtime());
                $user->save();
                $user = $user->makeVisible(['api_token']);
                $result = ['status' => 200, 'data' => ['user' => $user]];
            }
            else
            {
                $result = ['status' => 401];
            }

            return response($result);

        }
    }

    public function update(Request $request)
    {
        $user = User::find($request->get('id'));

        return response($user);
    }

    public function get($id)
    {
        $user = User::find($id);

        $result = ['status' => 200, 'data' => ['user' => $user]];

        return response($result);
    }

}