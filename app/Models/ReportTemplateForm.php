<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTemplateForm extends Model
{
    use SoftDeletes;

    public static $o_table = 'report_template_forms';//指定表名,外部调用
    protected $guarded = [];


}
