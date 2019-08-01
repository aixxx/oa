<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompanyAnnualRule
 *
 * @property int $id
 * @property int $company_id 公司id
 * @property int $rule_id 年假规则id
 * @property int $type 公司多对多关联分类1、年假规则
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class CompanyAnnualRule extends Model
{
    //
    protected $table = 'company_annual_rule';

    protected $fillable = ['company_id', 'rule_id', 'type'];

}
