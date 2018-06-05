<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Controller;
use App\User;

class VkontakteController extends Controller
{
    public function redirectToProvider() {
        if (auth()->check()) {
            return redirect()->to('/');
        }

        return \Socialite::driver('vkontakte')->redirect();
    }

    public function handleProviderCallback() {
        if (request()->has('error')) {
            return redirect()->to('/');
        }

        // Set VK photo size
        \Socialite::driver('vkontakte')->fields(['photo_200']);

        $userData = \Socialite::driver('vkontakte')->user();

        $user = User::whereVkontakteId($userData->id)->first();

        if (!$user) {
            if (!empty($userData->email)) {
                $user = User::whereEmail($userData->email)->first();
            }

            if (!$user) {
                $user = new User;
                info(print_r($userData, true));
                $user->name = $userData->name;
                $user->photo = $userData->user['photo_200'];
                if (!empty($userData->email)) {
                    $user->email = $userData->email;
                    $user->email_confirmed = true;
                }
            }
            $user->vkontakte_id = $userData->id;
            $user->save();
        }

        $user->auth_hash = request()->auth_hash;
        $user->save();

        auth()->guard()->login($user);

        return redirect()->to('/');
    }
}
