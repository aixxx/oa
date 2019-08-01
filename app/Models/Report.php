<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    //工作汇报，模板字段类型
    public static $reportTemplateFieldType = [
        1 => ['文本型', '选择此项可填写文字内容', 'text'],
        2 => ['数字型', '选择此项只能填写数字', 'number']
    ];


    /*
     * 关联点赞
     * */
    public function reportLike(){
        return $this->hasMany('App\Models\Like', 'relate_id', 'id')->where('type', 1);
    }

    /*
     * 关联评论
     * */
    public function reportComment(){
        return $this->hasMany('App\Models\Comments\TotalComment', 'relation_id', 'id')->where('type', 10);
    }

    /*
     * 关联汇报自定义信息数据
     * */
    public function hasManyReportContent(){
        return $this->hasMany('App\Models\ReportFieldInfo', 'report_id', 'id');
    }

}
