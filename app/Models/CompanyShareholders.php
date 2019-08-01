<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompanyShareholders
 *
 * @property int $id
 * @property int $company_id 关联企业
 * @property string|null $name 名称
 * @property int|null $shareholder_type 类型(1:自然人股东;2.法人股东)
 * @property int|null $certificate_type 证照/证件类型(1:非公示项;2.非公司企业法人营业执照;3.合伙企业营业执照;4.企业法人营业执照(公司))
 * @property string|null $id_number 证照/证件号码(非公示项/91230XXXX)
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 修改时间
 * @property int $status 状态1.有效；2.删除
 * @property-read \App\Models\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereCertificateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereShareholderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyShareholders whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompanyShareholders extends Model
{

    const SHAREHOLDER_PERSON = 1; //自然人
    const SHAREHOLDER_ENTERPRISE = 2; //法人股东

    const STATUS_NO_DELETE = 1;   //未删除
    const STATUS_DELETE    = 2;    //删除

    public static $shareholderTypeMap = [
        self::SHAREHOLDER_PERSON     => '自然人股东',
        self::SHAREHOLDER_ENTERPRISE => '法人股东',
    ];

    public static $certificateTypeMap = [
        '1'    => '非公示项',
        '2'    => '非公司企业法人营业执照',
        '3'    => '合伙企业营业执照',
        '4'    => '企业法人营业执照(公司)',
    ];

    public $fillable = [
        'name',
        'company_id',
        'shareholder_type',
        'certificate_type',
        'id_number',
        'status',
    ];


    public function company() {
        return $this->hasOne('App\Models\Company','id','company_id') ;
    }

    static public function getComment()
    {
        $map = [
            'name'             => '股东名称',
            'shareholder_type' => '类型',
            'certificate_type' => '证照/证件类型',
            'id_number'        => '证照/证件号码',
        ];

        return $map;
    }
}
