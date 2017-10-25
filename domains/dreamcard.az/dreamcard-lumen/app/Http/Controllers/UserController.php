<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 04:00
 */

namespace App\Http\Controllers;


use App\Photo;
use App\User;
use Hamcrest\ResultMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

                $user->api_token = md5(microtime());
                $user->save();
                $user = $user->makeVisible(['api_token']);
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

    public function logout()
    {
        $user = Auth::user();
        $user->api_token = null;
        $user->save();
        $result = ['status' => 200];
        return response($result);
    }

    public function update(Request $request)
    {
        $user = User::find($request->get('id'));
        if ($user)
        {
            if ($request->has('first_name'))
            {
                $user->first_name = $request->get('first_name');
            }

            if ($request->has('last_name'))
            {
                $user->last_name = $request->get('last_name');
            }

            if ($request->has('email'))
            {
                $user->email = $request->get('email');
            }

            if ($request->has('phone'))
            {
                $user->phone = $request->get('phone');
            }

            if ($request->has('password') && $request->has('prev_password'))
            {
                if (Hash::check($request->get('prev_password'), $user->getAuthPassword()))
                {
                    $user->password = app('hash')->make($request->get('password'));
                }
                else
                {
                    return response(['status' => 409]);
                }
            }

            if ($request->has('city_id'))
            {
                $user->city_id = $request->get('city_id');
            }

            if ($request->hasFile('photo'))
            {
                $photo = new Photo();
                $photo_result = $photo->upload($request->file('photo'), 'uploads/photos/users/');

                if ($photo_result == 200)
                {
                    $user->deletePhoto();
                    $user->photo_id = $photo->id;
                }
                else
                {
                    return response(['status' => $photo_result]);
                }
            }

            $user->save();
            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 408];
        }

        return response($result);
    }

    public function get($id)
    {
        $user = User::find($id);

        $result = ['status' => 200, 'data' => ['user' => $user]];

        return response($result);
    }

    public function getUsers()
    {
        $users = User::paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($users);
        return response($result);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->deletePhoto();
        $user->delete();
        $result = ['status' => 200];

        return response($result);
    }

}