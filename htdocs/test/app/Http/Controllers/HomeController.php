<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function site() {
        $role = 'noAuth';
        $ignoreCurrentPath = request()->path() == 'confirmation' ||
            request()->path() == 'agreement';

        $user = auth()->user();

        // Check if user has all required fields set before he can work
        if (auth()->check()) {
            if (!$ignoreCurrentPath && !auth()->user()->isValid()) {
                if ($user->isTeacher() && request()->path() != 'form/teacher') {
                    return redirect('/form/teacher');
                }
                else if ($user->isUser() && request()->path() != 'form/user') {
                    return redirect('/form/user');
                }
            }
            else if ($user->isValid()) {
                $isTeacherOnTeacherForm = $user->isTeacher() && request()->path() == 'form/teacher';
                $isUserOnUserForm = $user->isUser() && request()->path() == 'form/user';
                if ($isTeacherOnTeacherForm || $isUserOnUserForm) {
                    return redirect('/');
                }
            }
            $role = $user->isTeacher() ? 'teacher' : 'site';
        }
        return view('layouts/site', ['role' => $role]);
    }

    public function admin() {
        return view('layouts/admin');
    }
}
