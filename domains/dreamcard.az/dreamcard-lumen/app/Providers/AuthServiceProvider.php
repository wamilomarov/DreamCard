<?php

namespace App\Providers;

use App\Admin;
use App\Department;
use App\Partner;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $req = $request->json();
            if ($request->has('api_token') || $req->has('api_token')) {
                $user = User::where('api_token', $request->input('api_token'))->first();
                if ($user) {
                    return $user;
                } else {
                    $partner = Partner::where('api_token', $request->input('api_token'))->first();
                    if ($partner) {
                        return $partner;
                    } else {
                        $department = Department::where('api_token', $request->input('api_token'))->first();
                        if ($department) {
                            return $department;
                        } else {
                            $admin = Admin::where('api_token', $request->input('api_token'))->first();
                            if ($admin) {
                                return $admin;
                            } else {
                                return null;
                            }
                        }
                    }
                }
            } else {
                return null;
            }
        });
    }
}
