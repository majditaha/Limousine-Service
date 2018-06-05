<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Http\Resources\User as UserResource;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'check']);
    }

    protected function authenticated($request, $user)
    {
        $user->auth_hash = $request->auth_hash;
        $user->save();
        return response()->json(['success' => true]);
    }

    public function check()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->load('disciplineSubscriptions.discipline');
            return response()->json(new UserResource($user));
        }
        else {
            return response('Not authorized', 401);
        }
    }
}
