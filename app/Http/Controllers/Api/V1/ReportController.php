<?php
/**
 * Created by yyp.
 * User: yyp
 * Date: 2019/4/17
 * Time: 15:48
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\Report;
use App\Models\ReportRule;
use App\Repositories\ReportRepository;
use Illuminate\Http\Request;
use App\Constant\ConstFile;
//use Illuminate\Support\Facades\Auth;
use Auth;
use App\Models\User;

class ReportController extends BaseController
{
    public $report;
    public function __construct(ReportRepository $report)
    {
        $this->report = $report;
    }

    /*
     * 模版字段类型
     * */
    public function fieldType(){
        $field = Report::$reportTemplateFieldType;

        $data = [];
        foreach($field as $k=>$v){
            list($da['title'], $da['descript'], $da['type']) = $v;
            $data[$k] = $da;
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $data);
    }


    /*
     * 获取报告模版信息
     * */
    public function templateField(Request $request){
        $user = Auth::user();
        $id = $request->input('id',0);
        return $this->report->getTemplateField($id, $user);
    }


    /*
     * 模板列表
     * */
    public function templateList(Request $request){
        $user = Auth::user();
        $data = $this->report->templateList($user, $request->all());
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$data);
    }


    /*
     * 提交模板信息
     * */
    public function editTemplate(Request $request){
        $user = Auth::user();
        return $this->report->editTemplate($request->all(), $user);
    }


    /*
     * 模版信息
     * */
    public function templateInfo(Request $request){
        $id = $request->input('id',0);
        if(!$id){
            return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
        }

        $data = $this->report->templateInfo($id);//模板信息
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$data);
    }


    /*
     * 添加报告规则显示信息
     * */
    public function ruleInfo(Request $request){
        $id = $request->input('id',0);
        $rule_info = [];
        if($id){
            //修改查询报告规则信息
            $rule_info = ReportRule::find($id);
            $select_user = explode(',', $rule_info['select_user']);
            !empty($select_user) && $rule_info['select_user'] = User::whereIn('id', $select_user)->get(['id', 'chinese_name', 'avatar'])->toArray();
        }
        $user = Auth::user();
        $reportType = $this->report->templateList($user);//汇报类型
        $data = [
            'rule_id' => $id,
            'rule_info' => $rule_info,
            'reportType' => $reportType,
            'note' => array_values(ConstFile::$schedulePromptTypeList) //提醒时间
        ];

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$data);
    }


    /*
     * 添加修改报告规则
     * */
    public function editRule(Request $request){
        $id = $request->input('id', 0);
        $user = Auth::user();
        if($id){
            return $this->report->editRule($request->all(), $user);//修改报告统计规则
        }else{
            return $this->report->createRule($request->all(), $user);//添加报告统计规则
        }
    }


    /*
     * 获取汇报当前周期
     * */
    public function getCurrentCycle(Request $request){
        return $this->report->getCurrentCycle($request->all());
    }


    /*
     * 提交汇报数据
     * */
    public function editReport(Request $request){
        $user = Auth::user();
        return $this->report->editReport($request->all(), $user);
    }


    /*
     * 修改汇报详情
     * */
    public function editReportInfo(Request $request){
        $data = $this->report->reportInfo($request->id);
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$data);
    }


    /*
     * 汇报列表
     * */
    public function reportList(Request $request){
        $user = Auth::user();
        return $this->report->reportList($request->all(), $user);
    }


    /*
     * 汇报详情
     * */
    public function reportDetail(Request $request){
        if(!$request->all()) return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
        $user = Auth::user();
        return $this->report->reportDetail($request->all(), $user->id);
    }


    /*
     * 删除
     * */
    public function delReport(Request $request){
        $user = Auth::user();
        return $this->report->delReport($request->all(), $user);
    }


    /*
     * 读汇报
     * */
    public function readReport(Request $request){
        $user = Auth::user();
        return $this->report->readReport($request->all(), $user);
    }


    /*
     * 已读未读列表
     * */
    public function readList(Request $request){
        $user = Auth::user();
        return $this->report->readList($request->all(), $user);
    }


    /*
     * 提醒一下
     * */
    public function remindReader(Request $request){
        $user = Auth::user();
        return $this->report->remindReader($request->all(), $user);
    }


    /*
     * 我要写的汇报
     * */
    public function myNeedReport(){
        $user = Auth::user();
        return $this->report->myNeedReport($user);
    }


    /*
     * 我创建的规则
     * */
    public function myReportRule(){
        $user = Auth::user();
        return $this->report->myReportRule($user);
    }


    /*
     * 规则统计列表
     * */
    public function ruleStatisticsList(Request $request){
        $user = Auth::user();
        return $this->report->ruleStatisticsList($request->all(), $user);
    }


    /*
     * 获取上一个/下一个汇报时间点
     * */
    public function getPreOrAfterReportTime(Request $request){
        return $this->report->getPreOrAfterReportTime($request->all());
    }


    /*
     * 日志报表
     * */
    public function logReport(Request $request){
        return $this->report->logReport($request->all());
    }


    /*
     * 删除汇报模板
     * */
    public function delReportTemplate(Request $request){
        $user = Auth::user();
        return $this->report->delReportTemplate($request->all(), $user);
    }


    /*
     * 删除汇报规则
     * */
    public function delReportRule(Request $request){
        $user = Auth::user();
        return $this->report->delReportRule($request->all(), $user);
    }
}