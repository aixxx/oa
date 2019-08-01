<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompanyMainPersonnels
 *
 * @property int $id
 * @property int $company_id 关联企业
 * @property string|null $name 姓名
 * @property string|null $position 职位(董事/监事/经理等)
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 修改时间
 * @property int $status 状态1.有效；2.删除
 * @property-read \App\Models\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyMainPersonnels whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyMainPersonnels whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyMainPersonnels whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyMainPersonnels whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyMainPersonnels wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyMainPersonnels whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyMainPersonnels whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompanyMainPersonnels extends Model
{
    const STATUS_NO_DELETE = 1;   //未删除
    const STATUS_DELETE    = 2;    //删除

    public $fillable = [
        'name',
        'position',
        'company_id',
        'status',
    ];


    public function company() {
        return $this->hasOne('App\Models\Company','id','company_id') ;
    }

    static public function getComment()
    {
        $map = [
            'name'     => '姓名',
            'position' => '职位'
        ];

        return $map;
    }
}
