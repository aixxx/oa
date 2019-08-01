<?php

namespace App\Services;

use Request;
use Session;

trait AuthenticatesLogoutService
{
    public function logout(Request $request)
    {
        $this->guard()->logout();

        Session::forget($this->guard()->getName());

        Session::regenerate();

        return redirect('/admin/login');
    }
}
