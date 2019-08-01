<?php

namespace App\Exceptions;

use Illuminate\Http\Request;

trait AuthenticatesLogout
{
    public function logout(Request $request)
    {
        dd($this->guard()->getName());
        $this->guard()->logout();

        $request->session()->forget($this->guard()->getName());

        $request->session()->regenerate();

        return redirect('/');
    }
}
