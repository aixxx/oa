<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompanyEquityPledge
 *
 * @property int $id
 * @property int $company_id 关联企业
 * @property string|null $code 登记编号
 * @property string|null $pledgor 出质人
 * @property string|null $pledgor_id_number 出质人证照/证件号码
 * @property int|null $amount 出质股权数额
 * @property string|null $pledgee 质权人
 * @property string|null $pledgee_id_number 质权人证照/证件号码
 * @property string|null $register_date 股权出质设立登记日期
 * @property int|null $pledge_status 出质状态(1:有效;2.无效)
 * @property int $status 状态1.有效；2.删除
 * @property string|null $public_at 公示日期
 * @property \Carbon\Carbon|null $created_at 创建日期
 * @property \Carbon\Carbon|null $updated_at 修改日期
 * @property-read \App\Models\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge wherePledgeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge wherePledgee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge wherePledgeeIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge wherePledgor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge wherePledgorIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge wherePublicAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereRegisterDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyEquityPledge whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompanyEquityPledge extends Model
{
    const PLEDGE_STATUS_EFFECT  = 1; //生效
    const PLEDGE_STATUS_INVALID = 0; //失效

    const STATUS_NO_DELETE = 1;   //未删除
    const STATUS_DELETE    = 2;    //删除

    static public $pledgeStatusMap = [
        self::PLEDGE_STATUS_EFFECT => '生效',
        self::PLEDGE_STATUS_INVALID => '失效'
    ];

    protected $table = 'company_equity_pledge';

    public $fillable = [
        'company_id',
        'code',
        'pledgor',
        'pledgor_id_number',
        'amount',
        'pledgee',
        'pledgee_id_number',
        'register_date',
        'pledge_status',
        'status',
        'public_at',
    ];


    public function company() {
        return $this->hasOne('App\Models\Company','id','company_id') ;
    }



    static public function getComment()
    {
        $map = [
            'code'              => '登记编号',
            'pledgor'           => '出质人',
            'pledgor_id_number' => '出质人证照/证件号码',
            'amount'            => '出质股权数额',
            'pledgee'           => '质权人',
            'pledgee_id_number' => '质权人证照/证件号码',
            'register_date'     => '股权出质设立登记日期',
            'pledge_status'     => '出质状态',
            'public_at'         => '公示日期'
        ];
        return $map;
    }
}
