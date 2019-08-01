<?php

namespace XinApp\Http\Controllers\Api;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Request;


class AuthController extends BaseController
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;


    /**
     * The authentication guard that should be used.
     *
     * @var string
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate()
    {


        $payload = [
            'email' => Request::get('email'),
            'password' =>Request::get('password')
        ];//dd( $payload);
        try {
            if (!$token = JWTAuth::attempt($payload)) {
                return response()->json(['error' => 'token_not_provided'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => '不能创建token'], 500);
        }
        $data['token']=$token;
        return  returnJson($message='ok', $code = 200,$data);
    }




}