<?php
/**
 * Created by PhpStorm.
 * User: echo
 * Date: 2017/12/17
 * Time: 15:06
 */

namespace App\Http\Services;
use App\Constant\ConstFile;
use Illuminate\Support\Facades\Redis;
use Mrgoon\AliSms\AliSms;
use Auth;

trait SmsTrait
{
    /**
     * 1.登录确认验证码 SMS_7816658 验证码${code}，您正在登录${product}，若非本人操作，请勿泄露。
     * 2.用户注册验证码 SMS_7816656 验证码${code}，您正在注册成为${product}用户，感谢您的支持！
     * 3.修改密码验证码 SMS_7816654 验证码${code}，您正在尝试修改${product}登录密码，请妥善保管账户信息。
     * 4.身份验证验证码 SMS_7816660 验证码${code}，您正在进行${product}身份验证，打死不要告诉别人哦！
     * 5.信息变更验证码 SMS_7816653 验证码${code}，您正在尝试变更${product}重要信息，请妥善保管账户信息。
     */


    /**
     * @TODO 发送短信验证码:
     * @param $mobile
     * @return string
     */
    function traitSendSmsCode($mobile,$type){
        $templateCode=$this->traitTemplateCode($type);//获取模板id
        $this->checkMobile($mobile);//验证手机号码
        $key='users_'.date('YmdH').'_'.$mobile;
        if($this->traitGetCacheCode($key,$flag = false )){
            throw  new \Exception('请使用上一次验证码', '201');
        }
        $code  = $this->traitCreateCode();
        $aliSms = new AliSms();
        $data = array('code'=> $code,'product'=>'ERP');
        $send_res = $response = $aliSms->sendSms($mobile,$templateCode,$data);
        if( $send_res ){
            $this->traitSetCacheCode( $key , $code );
            return true;
        }else{
            return false;
        }
    }


    /**
     * 通过手机号码自动获取key值
     */
    function checkUsersCode($mobile,$mCode){
       /* if(Auth::id()){
            $key='users_'.Auth::id();
        }else{
            $key='users_'.$mobile;
        }*/
        $key='users_'.date('YmdH').'_'.$mobile;
        $rCode=$this->traitGetCacheCode($key,$flag = false );
        $rt=$this->traitCheckCode( $rCode , $mCode );
        return $rt;

    }

    /**
     * @TODO 获取缓存值
     * @param $key
     * @return bool
     */

    function traitGetCacheCode( $key , $flag = true ){
        if( !empty( $key ) ){
            $val =  Redis::get( $key );
            if( $flag === true ){
               $this->traitClearCache( $key );
            }
            return $val;
        }else{
            return false;
        }
    }

    function traitClearCache( $key ){
        if( is_array( $key ) ){
            foreach( $key as $v ){
                Redis::del( $v ) ;
            }
        }else{
            Redis::del( $key ) ;
        }

    }



    /*function traitCreateImgCode(){
        $builder = new CaptchaBuilder(null,new PhraseBuilder(4,'0123456789'));
        $json_arr['code']=$builder->getPhrase(6);

        $builder->build(100, 40);
        ob_start();
        $builder->output();
        $fileContent = ob_get_contents();
        ob_end_clean();
        $json_arr['img'] = "data:image/jpeg;base64," . base64_encode($fileContent);
        return $json_arr;
    }*/

    /**
     * @TODO 生成6位数验证码
     * @param int $num
     * @return string
     */
    function traitCreateCode($num = 4 , $type = 'int'){
        $code = '';
        $code_string = "0123456789abcdefghijklmnopqrstuvwxyz";
        for( $i=0 ; $i < $num ; $i++ ){
            if( $type == 'int' ){
                $code .= rand(0,9);
            }else{
                $code .= $code_string[rand(0,35)];
            }
        }
        //session(['pay.sms.code'=>$code]);
        return $code;
    }

    /**
     * @TODO 缓存code值
     * @param $key
     * @param $val
     * @param int $time
     * @return bool
     */
    function traitSetCacheCode( $key , $val , $time = 600 ){
        if( !empty( $key ) && !empty( $val )){
            Redis::set( $key , $val );
            if( $time > 0 ){
                Redis::expire( $key , $time );
            }
            return true;
        }else{
            return false;
        }

    }

    /**
     * @TODO 验证验证码是否一致
     * @param $rCode
     * @param $mCode
     * @return bool
     */
    function traitCheckCode( $rCode , $mCode ){
        $rCode=strtolower($rCode);
        $mCode=strtolower($mCode);
        if( $rCode && $rCode == $mCode ){
            return true;
        }else{
            return false;
        }
    }
    //根据不同的type获取模板code
    function traitTemplateCode($type){
        switch ($type){
            case 2:
                $templateCode=ConstFile::SENDREGISTERCODE;
                break;
            case 3:
                $templateCode=ConstFile::SENDCHANGEPWDCODE;
                break;
            case 4:
                $templateCode=ConstFile::SENDAUTHENTICATIONCODE;
                break;
            case 5:
                $templateCode=ConstFile::SENDINFOSEND;
                break;
            default:
                $templateCode=ConstFile::SENDLOGINCODE;
        }
        return $templateCode;
    }
    function checkMobile($mobile){
        // 如果是手机号格式则调用手机号登陆
        if(!$mobile){
            throw  new \Exception('手机号码不能为空', '400');
        }
        if(!preg_match("/^1[3456789]\d{9}$/", $mobile)){
            throw  new \Exception('手机号码格式不正确', '400');
        }

        return true;
    }


}
