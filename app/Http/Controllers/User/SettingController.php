<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Coffer\CertController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return view("user.setting.edit");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'originalpassword'         => 'required',
            'newpassword'              => 'required|min:7|confirmed',
            'newpassword_confirmation' => 'required',
        ], [
            'originalpassword.required'         => '原密码必填',
            'newpassword.required'              => '新密码必填',
            'newpassword.min'                   => '新密码长度最小为7位',
            'newpassword.confirmed'             => '两次密码输入不一致',
            'newpassword_confirmation.required' => '确认密码必填',
        ]);

        $originalPassword = $request->get('originalpassword');
        $newPassword      = $request->get('newpassword');

        $userOrm = User::findOrFail($id);

        if (!\Hash::check($originalPassword, $userOrm->password)) {
            return back()->withErrors(['originalpassword' => '原密码错误'])->withInput();
        }

        $updateResult = $userOrm->update(['password' => bcrypt($newPassword)]);

        if (!$updateResult) {
            return back()->with('failed', '新密码设置失败')->withInput();
        }
        CertController::updateAfterVaildPassword($originalPassword, $newPassword);
        \Auth::logout();
        return redirect()->route('login')->with('message', "重置密码成功请重新登录！");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
