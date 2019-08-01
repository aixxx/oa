<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/9/3
 * Time: 下午6:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserBankCard
 *
 * @property int $id
 * @property int $user_id 银行卡持有人
 * @property string $card_num 银行卡号
 * @property string $bank 开户行
 * @property string $branch_bank 支行名称
 * @property string $bank_province 银行卡属地（省）
 * @property string $bank_city 银行卡属地（市）
 * @property int|null $bank_type 银行卡类型 1:主卡 2副卡
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string|null $deleted_at
 * @property string $bank_abbr 银行名称简写
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBankCard onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereBankAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereBankCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereBankProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereBankType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereBranchBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereCardNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserBankCard whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBankCard withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserBankCard withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\User $user
 */
class UserBankCard extends Model
{
    use SoftDeletes;
    protected $datas = ['deleted_at'];
    protected $table = 'user_bank_card';
    //银行卡类型
    const BANK_CARD_TYPE_MAIN = 1;//主卡
    const BANK_CARD_TYPE_VICE = 2;//副卡
    public $fillable = [
        'user_id',
        'card_num',
        'bank',
        'branch_bank',
        'bank_province',
        'bank_city',
        'bank_type',
        'bank_abbr'
    ];

    public static $banks = [
        'ICBC'    => '中国工商银行',
        'ABC'     => '中国农业银行',
        'BOC'     => '中国银行',
        'CCB'     => '中国建设银行',
        'COMM'    => '交通银行',
        'CITIC'   => '中信银行',
        'CEB'     => '中国光大银行',
        'HXBANK'  => '华夏银行',
        'CMBC'    => '中国民生银行',
        'GDB'     => '广东发展银行',
        'CMB'     => '招商银行',
        'CIB'     => '兴业银行',
        'SPDB'    => '上海浦东发展银行',
        'EGBANK'  => '恒丰银行',
        'CZBANK'  => '浙商银行',
        'BOHAIB'  => '渤海银行',
        'SCB'     => '渣打银行',
        'PSBC'    => '中国邮政储蓄银行',
        'SHBANK'  => '上海银行',
        'SHRCB'   => '上海农村商业银行',
        'SPABANK' => '平安银行'
    ];

    public static function createBankCard($userId, $cardNum, $bank, $branchBank, $bankProvince, $bankCity, $bankType)
    {
        if (!$bankCity) {
            return ['status' => 'error', 'message' => "请填写银行卡属地"];
        }

        //主副卡只能各有一张
        if ($bankType == UserBankCard::BANK_CARD_TYPE_MAIN) {
            $BankCard = UserBankCard::getByTypeAndUser($userId, UserBankCard::BANK_CARD_TYPE_MAIN);
            if (count($BankCard) >= 1) {
                return ['status' => 'error', 'message' => "主卡已经存在，请重新选择！"];
            }
        }

        if ($bankType == UserBankCard::BANK_CARD_TYPE_VICE) {
            $BankCard = UserBankCard::getByTypeAndUser($userId, UserBankCard::BANK_CARD_TYPE_VICE);
            if (count($BankCard) >= 1) {
                return ['status' => 'error', 'message' => "副卡已经存在，请重新选择！"];
            }
        }

//        $url    = 'https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?cardBinCheck=true&cardNo=' . $cardNum;
//        $result = json_decode(self::request_post($url));
//
//        if (!isset($result->bank)) {
//            return ['status' => 'error', 'message' => "该卡不存在！请重新添加"];
//        }
//        if ($result->cardType != "DC") {
//            return ['status' => 'error', 'message' => "该卡不是储蓄卡！请换卡添加"];
//        }
//
//        if ($bank != $result->bank) {
//            return ['status' => 'error', 'message' => "开户行填写错误！请重新选择"];
//        }
//
//
//        //两张银行卡不能是同一家银行
//        $validBank = self::validBank($userId, $result->bank);
//        if (!$validBank) {
//            return ['status' => 'error', 'message' => '该开户行已存在,请重新添加！'];
//        }

        $data['user_id']       = $userId;
        $data['card_num']      = encrypt($cardNum);
        $data['bank']          = encrypt(self::$banks[$bank]);
        $data['branch_bank']   = encrypt($branchBank) ?: '';
        $data['bank_province'] = encrypt($bankProvince) ?: '';
        $data['bank_city']     = encrypt($bankCity);
        $data['bank_type']     = $bankType;
        //$data['bank_abbr']     = $result->bank;
        $data['bank_abbr']     = 'default';
        $bankCard              = new UserBankCard();
        $bankCard->fill($data);
        if ($bankCard->save()) {
            return ['status' => 'success', 'message' => "银行卡添加成功"];
        } else {
            return ['status' => 'error', 'message' => "银行卡添加失败"];
        }
    }

    public static function deleteBankCard($bankCard)
    {
        $result = $bankCard->delete();
        return $result;
    }

    public static function request_post($url = '', $ispost = true, $post_data = array())
    {
        if (empty($url)) {
            return false;
        }

        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);
        $key       = md5(base64_encode($post_data));
        if ($ispost) {
            $url = $url;
        } else {
            $url = $url . '?' . $post_data;
        }

        $curlPost = 'key=' . $key;
        header("Content-type: text/html; charset=utf-8");
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        }
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }

    public static function getByTypeAndUser($userId, $bankType)
    {
        return self::where('user_id', $userId)->where('bank_type', $bankType)->get();
    }

    /*
     * 校验开户行是否已存在
     */
    public static function validBank($userId, $bankAbbr)
    {
        $bankCardSelect = self::where('user_id', $userId)->get();
        if ($bankCardSelect->isNotEmpty()) {
            $bankAbbrInfo = $bankCardSelect->pluck('bank_abbr')->toArray();
            if ($bankAbbr) {
                if (in_array($bankAbbr, $bankAbbrInfo)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /**
     * 按照几位数一组展示卡号
     * @param $num
     * @param $count
     * @return string
     */
    public static function formatBankCardShowType($num,$count)
    {
        $arr=str_split($num,$count);
        $num=implode(' ',$arr);
        return $num;
    }
}