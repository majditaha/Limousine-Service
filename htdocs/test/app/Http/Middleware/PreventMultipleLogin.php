<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class PreventMultipleLogin
{
    /**
     * The authentication factory implementation.
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
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // For api to work from different places
        if (empty($request->header('referer'))) {
            return $next($request);
        }

        $hash = md5($request->header('user-agent') . $request->ip());

        $request->auth_hash = $hash;

        if (!$request->user()) {
            return $next($request);
        }

        if ($request->user()->auth_hash && $request->user()->auth_hash != $hash) {
            $this->logout($request);
        }
        return $next($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function logout($request)
    {
        if (!starts_with($request->path(), 'api')) {
            $this->auth->logout();

            $request->session()->flush();
        }

        throw new AuthenticationException;
    }
}
