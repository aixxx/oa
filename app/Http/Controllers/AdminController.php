<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function authCheck()
    {
        $name = session('admin_name');
        if (!$name) {
            Header('Location:' . route('admin-login'));
        }
    }
}
