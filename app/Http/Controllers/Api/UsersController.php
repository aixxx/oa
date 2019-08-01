<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Models\OauthClients;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Certificate;
use Auth;

class UsersController extends Controller
{

    public $successStatus = 200;



    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function logins(Request $request){
        $param = $request->all();


        $oauthClientsFirst = OauthClients::where([
            'name' => 'APP'
        ])->first();
        $http = new \GuzzleHttp\Client;
        $response = $http->post(config('app.url').'/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oauthClientsFirst->id,
                'client_secret' => $oauthClientsFirst->secret,
                'email' => $param['email'],
                'password' => $param['password'],
                'scope' => '',
            ],
            'http_errors'=>false,
        ]);
        dd($response);
        if(200 == $response->getStatusCode()){
            //$rt=Auth::attempt(['user_name' =>$param['username'], 'password' => $param['password']]);

            $responseArray = json_decode($response->getBody()->getContents(),true);
            if( null == $responseArray){
                throw  new \Exception('获取认证信息失败', '400');
            }
        }else{
            throw  new \Exception('获取认证信息失败', '400');
        }
        dd( $responseArray );
    }


    /**
     * 当前用户
     * @return mixed
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $me   = User::with('departments', 'certificate')->findOrFail($user->id)->toArray();
        return $this->success($me);
    }

    /*
     * 公钥加密
     */
    public function encrypt(Request $request)
    {
        $data = $request->post('data');
        $uid  = $request->post('uid');

        $certificate = Certificate::firstUserCertOrFail($uid);

        $pu_key = openssl_pkey_get_public($certificate->public_key);

        $isOkay = openssl_public_encrypt($data, $encrypted, $pu_key);//公钥加密

        if (!$isOkay) {
            $this->failed('数据加密失败');
        }
        $encrypted = base64_encode($encrypted);

        return $this->success($encrypted);
    }

    /*
     * 使用私钥解密
     */
    public function decrypt(Request $request)
    {
        $data = $request->post('data');
        $uid  = $request->post('uid');

        $certificate = Certificate::firstUserCertOrFail($uid);
        $pi_key      = openssl_pkey_get_private($certificate->private_key);

        $isOkay = openssl_private_decrypt(base64_decode($data), $decrypted, $pi_key);//私钥解密

        if (!$isOkay) {
            $this->failed('数据解密失败');
        }

        return $this->success($decrypted);
    }


    public function index(Request $request)
    {

    }

    public function show($id)
    {
        $user = User::findOrFail($id)->with('departments')->first();
        return $this->success($user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return mixed
     */
    public function store(UserRequest $request)
    {

        $user = new User;
        $user->fill($request->all());

        $user->name = $user->english_name;

        //FIXME: 默认密码，测试方便
        $user->password = bcrypt('password');

        if ($user->save()) {
            //开通企业微信
            $work     = app('wechat.work.contacts');
            $response = $work->user->create([
                'name'         => $user->chinese_name,
                'userid'       => $user->english_name,
                'english_name' => $user->english_name,
                'mobile'       => $user->mobile,
                'department'   => [1],
            ]);

            //创建证书
            Certificate::userIssue($user->id);
            return $this->created();
        }
        return $this->failed($user->getErrors());
    }

    public function edit($id)
    {
        //
    }

    public function update($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }


}
