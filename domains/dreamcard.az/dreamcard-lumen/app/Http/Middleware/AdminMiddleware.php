<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 29-Oct-17
 * Time: 00:33
 */

namespace App\Http\Middleware;


class AdminMiddleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->user()->getTable() != 'users' && $request->user()->status == 1) {
            return response(['status' => 401]);
        }

        return $next($request);
    }
}