<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 28-Oct-17
 * Time: 03:37
 */

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;

class DepartmentMiddleware
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
        if ($request->user()->getTable() != 'departments') {
            return response(['status' => 401]);
        }

        return $next($request);
    }
}