<?php
/**
 * Created by yyp.
 * User: yyp
 * Date: 2019/4/17
 * Time: 17:32
 */

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Like;
use App\Models\Message\Message;
use App\Models\Report;
use App\Models\ReportFieldInfo;
use App\Models\ReportRule;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateForm;
use App\Models\Schedules\Schedules;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\DepartUser;
use Exception;
use Carbon\Carbon;
use App\Models\Comments\TotalComment;
use App\Services\WorkflowUserService;
use App\Http\Traits\HandleTableTrait;


class ReportRepository extends ParentRepository
{
    const YES = 1;
    const NO = 0;
    const REPORT_MESSAGE_TYPE = 5;//汇报消息类型
    const CYCLE_MONTH = 3;//月周期
    const CYCLE_WEEK = 2;//周周期
    const CYCLE_DAY = 1;//日周期

    //接收人设置
    public $receive_level_ = [
        1 => '一级部门主管',
        2 => '二级部门主管',
        3 => '三级部门主管',
        4 => '四级部门主管'
    ];

    //use HandleTableTrait;

    public $limit; //默认分页展示数量
    public $default_take = 100;//无限制默认读取数量
    public function __construct(){
        $this->limit = 10;
    }

    /*
     * 模板详情
     * */
    public function templateInfo($id){
        return ReportTemplate::with(['formField'=>function($query){
                $query->select('id', 'template_id', 'field', 'field_name', 'field_type', 'field_value', 'field_default_value', 'required');
            }])
            ->where('id', $id)
            ->select('id', 'template_name')
            ->get()->toArray();
    }


    /*
     * 模板列表
     * */
    public function templateList($user, $param=[]){
        $data = [];
        if(!empty($param['type'])){
            //管理中只显示未删除的模板
            $data = ReportTemplate::where('company_id', $user->company_id)->take($this->default_take)->select('id','template_name','deleted_at')->get()->toArray();//模板数据
        }else{
            //非管理页面的模板列表需判断是否关联规则，若关联未删除的规则，则显示
            $template = ReportTemplate::withTrashed()->where('company_id', $user->company_id)->take($this->default_take)->get(['id','template_name','deleted_at']);//模板数据

            if(!empty($template)){
                $ids = array_column($template->toArray(), 'id');
                //$sql = 'select id, report_type, select_user from report_rules where report_type in('.implode(',', $ids).') and deleted_at is null and find_in_set('.$user->id.', `select_user`)';
                //$rule = DB::select($sql);

                //$rule = ReportRule::whereIn('report_type', $ids)->whereRaw('find_in_set('.$user->id.', `select_user`)')->get(['id', 'report_type', 'select_user']);

                $uid = $user->id;
                $depart = DepartUser::where('user_id', $uid)->value('department_id');//我所属的部门
                $rule = ReportRule::whereIn('report_type', $ids);

                $depart = collect($depart)->toArray();
                if(!empty($depart)){
                    //选择了用户所属部门的规则也需要统计
                    $rule->where(function ($query) use($uid, $depart){
                        $query->whereRaw('find_in_set('.$uid.', select_user)');
                        if(count($depart) > 0){
                            $depart = array_unique(array_filter($depart));
                            foreach ($depart as $v){
                                $query->orWhereRaw('find_in_set('.$v.', select_department)');
                            }
                        }
                    });
                }else{
                    $rule->whereRaw('find_in_set('.$uid.', select_user)');
                }
                $rule = $rule->select('id', 'report_type', 'select_user')
                    ->get(); //print_r($rule);die;

                foreach ($template as $k=>$v){
                    if(!$v['deleted_at']){
                        $data[$v['id']] = $v;
                    }else{
                        if(!empty($rule)){
                            foreach ($rule as $vv){
                                if($v['deleted_at'] && $v['id'] == $vv->report_type){
                                    $data[$v['id']] = $v;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }


    /*
     * 添加修改模板
     * */
    public function editTemplate($data, $user){
        try{
            $error = $this->checkTempData($data);//验证数据
            throw_if($error, new Exception($error));

            $res = self::NO;
            DB::transaction(function () use ($data, $user, &$res) {
                //1.添加修改模板
                $field = json_decode(htmlspecialchars_decode($data['field']), true);
                $del = !empty($data['del']) ? explode(',', $data['del']) : [];//删除的字段id
                $da = [
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'template_name' => $data['template_name'],
                ];

                $is_up = self::NO;
                if(!empty($data['id'])){
                    //修改
                    $res = ReportTemplate::where([['company_id', $user->company_id], ['id', $data['id']]])->update($da);
                    $id = $data['id'];
                    $is_up = self::YES;
                    !empty($del) && $res = ReportTemplateForm::where('template_id', $id)->whereIn('id', $del)->delete();
                }else{
                    //添加
                    $res = ReportTemplate::create($da);
                    $id = $res->id;
                }

                //2.修改添加模版内容信息
                $this->editTemplateForm($is_up,$id,$field);
            });
        } catch (Exception $e){
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
    }


    /*
     * 修改添加模版内容信息
     * */
    public function editTemplateForm($is_up, $id, $field){
        if(!empty($field)){
            $time = date('Y-m-d H:i:s', time());

            $add_field = $save_field = [];
            foreach($field as $v){
                $da = [
                    'field' => $v['field'],
                    'field_name' => $v['field_name'],
                    'field_type' => $v['field_type'],
                    'field_value' => $v['field_value'],
                    'required' => $v['required'],
                    'updated_at' => $time
                ];
                if(!empty($v['id']) && $is_up){
                    //修改
                    $save_field[] = array_merge(['id'=>$v['id']], $da);
                }else{
                    //添加
                    $da['template_id'] = $id;
                    $da['created_at'] = $time;
                    $add_field[] = $da;
                }
            }
            if(!empty($add_field)){
                ReportTemplateForm::insert($add_field);
            }
            if(!empty($save_field)){
                $this->updateBatch(ReportTemplateForm::$o_table,$save_field);
            }
        }
    }


    /*
     * 获取模板字段
     * */
    public function getTemplateField($id, $user){
        try{
            throw_if(empty($id), new Exception(ConstFile::API_PARAMETER_MISS));
            $data = ReportTemplateForm::where([['template_id', $id]])->get(['id','template_id','field','field_name','field_type','field_value','required']);//汇报模板自定义字段

            $level = ReportRule::where('report_type', $id)->max('receive_level');//模板对应的统计规则选择的接收人设置(所有规则中选择的最大级别)
            $primary_user = [];
            if($level > 0){
                $primary_user_path = WorkflowUserService::fetchUserPrimaryDeptPath($user->id, 1);//多层上级部门信息
                $primary_depart = array_column(array_slice($primary_user_path, -$level), 'id'); //获取指定级别的上级部门
                $primary_user = WorkflowUserService::fetchMoreMasterInfoByDepartment($primary_depart);//指定级别上层领导
            }
            collect($data)->each(function(&$v){
                $v->field_type_value = Report::$reportTemplateFieldType[$v->field_type][2];
            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, ['content'=>$data, 'primary_user'=>$primary_user]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 删除模板
     * */
    public function delReportTemplate($id, $user){
        try {
            throw_if(empty($id), new Exception(ConstFile::API_PARAMETER_MISS));

            $info = ReportTemplate::find($id)->first();
            throw_if(empty($info), new Exception(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE));
            throw_if($info->user_id != $user->id, new Exception('不可以删除别人的汇报模板'));

            $res = self::NO;
            DB::transaction(function () use ($info, &$res) {
                $res = ReportTemplate::where('id', $info->id)->delete();
                //$res = ReportTemplateForm::where('template_id', $info->id)->delete();//不删除模板字段信息，区别于模板修改中的删除
            });
            if(!$res){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 添加统计规则
     * */
    public function createRule($data, $user){
        try{
            $error = $this->checkData($data);//验证数据
            throw_if($error, new Exception($error));

            $res = self::NO;
            DB::transaction(function () use ($data, $user, &$res) {
                //创建规则
                $da = $this->fillReportRuleData($data, $user);
                $res = ReportRule::create($da);
            });
            if(!$res){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 修改统计规则
     * */
    public function editRule($data, $user){
        try{
            $error = $this->checkData($data);//验证数据
            throw_if($error, new Exception($error));

            $res = self::NO;
            DB::transaction(function () use ($data, $user, &$res) {
                $da = $this->fillReportRuleData($data, $user);
                $res = ReportRule::where(['company_id'=>$user->company_id, 'id'=>$data['id']])->update($da);
            });
            if(!$res){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 删除规则
     * */
    public function delReportRule($id, $user){
        try{
            throw_if(!$id, new Exception(ConstFile::API_PARAMETER_MISS));

            $info = ReportRule::find($id)->first()->toArray();
            throw_if(empty($info), new Exception(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE));
            throw_if($info['user_id'] != $user->id, new Exception('不可以删除别人的汇报规则'));

            $res = self::NO;
            DB::transaction(function () use ($info, &$res) {
                $res = ReportRule::where('id', $info['id'])->delete();
            });
            if(!$res){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 汇报数据添加修改
     * */
    public function editReport($data, $user){
        try{
            $error = $this->checkReportData($data);//验证数据
            throw_if($error, new Exception($error));

            //添加修改报告
            $res = $id = 0;
            DB::transaction(function () use ($data, $user, &$res, &$id) {
                $da = [
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'template_id' => $data['template_id'],
                    //'content' => htmlspecialchars($data['content']),
                    'img' => $data['img'],
                    'accessory' => $data['accessory'],
                    'remark' => $data['remark'],
                    'select_depart' => isset($data['select_depart']) ? $data['select_depart'] : '',//选择的部门
                    'select_user' => isset($data['select_user']) ? $data['select_user'] : ''//选择的员工
                ];

                $select_user = array_filter(explode(',', $data['select_user']));//选择员工
                if(!empty($data['cc_ids'])){//抄送人
                    $cc_ids = array_unique(array_filter($data['cc_ids']));
		            $da['cc_ids'] = implode(',', $cc_ids);
                    $select_user = array_unique(array_merge($select_user, $cc_ids));
                }

                if(!empty($data['id'])){
                    //修改汇报 TODO（暂不做）
                    $field = ReportFieldInfo::where(['report_id'=>$data['id']])->select()->get();//获取汇报已提交字段信息
                    $res = Report::find($data['id'])->fill($da)->save();
                    $id = $data['id'];
                }else{
                    //批量保存汇报提交的自定义数据
                    $res = Report::create($da);
                    $id = $res->id;

                    $content = htmlspecialchars_decode($data['content']);
                    $content = json_decode($content,true);
                    $max_field_id = ReportFieldInfo::max('id');
                    $cont = [];
                    foreach ($content as $k=>$v){
                        $tem['id'] = $max_field_id+$k+1;
                        $tem['template_id'] = $data['template_id'];
                        $tem['report_id'] = $id;
                        $tem['field_id'] = $v['id'];
                        unset($v['id']);
                        $tem = array_merge($tem, $v);
                        $cont[] = $tem;
                    }
                    $res = ReportFieldInfo::insert($cont);
                }

                if(!empty($select_user)){
                    foreach ($select_user as $v){
                        $message[] = [
                            'receiver_id' => $v,//接收者（申请人）
                            'sender_id' => $user->id,//发送这（最后审批人）
                            'content'=> $user->chinese_name.'提交了汇报，请查看',//内容（审批title）
                            'type' => self::REPORT_MESSAGE_TYPE,
                            'relation_id' => $id,
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'updated_at' => date('Y-m-d H:i:s', time())
                        ];
                    }
                    $res = Message::insert($message);
                }
            });
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 汇报详情
     * */
    public function reportInfo($id){
        if(!$id) return [];
        $report = Report::where('id', $id)->with(['hasManyReportContent'=>function($query){
            $query->select('id','report_id', 'field', 'field_name', 'field_type_value', 'field_value', 'required', 'schedule_id');
        }])->first();

        if(empty($report)){
            return [];
        }else{
            //$data['content'] = json_decode(stripslashes(htmlspecialchars_decode($data['content'])));
            $report->content = collect($report)->get('has_many_report_content');
            $report->select_user = explode(',', $report->select_user);
            $report->read = explode(',', $report->read);
            $report->cc_ids = explode(',', $report->cc_ids);
            $report = collect($report)->except(['has_many_report_content']);

            return $report;
        }
    }


    /*
     * 汇报列表
     * */
    public function reportList($data, $user){
        $tag = '';
        $uid = $user->id;

        try{
            $res = $this->checkReportListData($data);//验证数据
            if(!empty($res)){
                $where = $res['where'];
            }
            $list = Report::with(['hasManyReportContent'=>function($query){
                $query->select('id','report_id', 'field', 'field_name', 'field_type_value', 'field_value', 'required', 'schedule_id');
            }]);
            $res['tag'] && $tag = $res['tag'];

            if(!empty($data['user_id'])){
                //发布者条件
                $list->where('reports.user_id', $data['user_id']);
                $user = User::where('id',$data['user_id'])->pluck('chinese_name')->toArray();
                if($res['tag']){
                    $tag = $user[0].'的'.$res['tag'];
                }else{
                    $tag = $user[0].'的日志';
                }
            }else if(!empty($data['is_my_send'])){
                //我发出的
                $list->where('reports.user_id', $uid);
                //$tag = '我发出的日志';
            }else if(!empty($data['unread'])){
                //是否选择未读(我未读的)
                $list->where(function($query) use ($uid){
                    $query->whereRaw('!find_in_set('.$uid.', `read`)')->orWhereNull('read');
                })->whereRaw('find_in_set('.$uid.', select_user)');
                //$tag = '我未读的日志';
            }else{
                //默认条件，显示与自己相关的汇报
                $list->where(function ($query) use ($uid){
                    $query->where('reports.user_id', $uid)->orWhereRaw('find_in_set('.$uid.', select_user)');
                });
            }
            $list->leftJoin('users', 'users.id', '=', 'reports.user_id')
                ->leftJoin('report_templates', 'report_templates.id', '=', 'reports.template_id')
                ->select('reports.*', 'users.chinese_name', 'users.position', 'users.employee_num', 'users.avatar', 'report_templates.template_name')->orderBy('reports.created_at', 'desc')->orderBy('reports.id', 'desc');

            if(!empty($where)){
                $list->where($where);
            }
            $list = $list->paginate($data['limit'])->toArray();

            //未读数量
            $count = Report::where(function($query) use ($uid){
                $query->whereRaw('!find_in_set('.$uid.', `read`)')->orWhereNull('read');
            })->whereRaw('find_in_set('.$uid.', select_user)')->count();

            $result['tag'] = $tag;
            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['unread_num'] = $count;
            $result['data'] = $this->reportListDetail($list['data'], $uid);

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 汇报列表详细信息处理
     * */
    public function reportListDetail($data, $uid){
        if(!empty($data)){
            $user_ids = array_unique(array_column($data, 'user_id'));
            $report_ids = array_unique(array_column($data, 'id'));
            if(empty($user_ids) || empty($report_ids)){
                return [];
            }

            //部门信息
            $user_info = DepartUser::whereIn('user_id', $user_ids)
                ->leftJoin('departments', 'departments.id', '=', 'department_user.department_id')
                ->pluck('departments.name','user_id')
                ->toArray();

            //点赞列表
            $likes = Like::where('likes.type', 1)->whereIn('likes.relate_id', $report_ids)
                ->leftJoin('users', 'users.id', '=', 'likes.user_id')
                ->select('likes.relate_id','likes.user_id','users.chinese_name')
                ->get()->toArray();

            //评论列表
            $comment = TotalComment::where('total_comment.type', 10)->whereNull('total_comment.deleted_at')->whereIn('total_comment.relation_id', $report_ids)
                ->leftJoin('users', 'users.id', '=', 'total_comment.uid')
                ->select('total_comment.id','total_comment.relation_id','total_comment.uid','users.chinese_name', 'total_comment.comment_text')
                ->get()->toArray();
            $comment = array_field_as_key($comment, 'relation_id');

            foreach ($data as &$v){
                $v['likes'] = $v['comment'] = $v['cont'] = [];
                $v['is_like'] = $v['is_read'] = self::NO;

                !empty($comment[$v['id']]) && $v['comment'][] = $comment[$v['id']];//保存最后一条评论

                $v['dname'] = empty($user_info[$v['user_id']]) ? '' : $user_info[$v['user_id']];
                $v['send_num'] = count(array_filter(explode(',', $v['select_user'])));
                $v['read_num'] = count(array_filter(explode(',', $v['read'])));
                $v['comment_type'] = 10;//汇报评论指定值

                if(!empty($likes)){
                    foreach($likes as $v1){
                        if($v1['relate_id'] == $v['id']){
                            $v['likes'][] = $v1;
                            $v1['user_id'] == $uid && $v['is_like'] = self::YES;//点赞信息
                        }
                    }
                }
                if(!empty($v['read']) && in_array($uid, explode(',', $v['read']))){
                    $v['is_read'] = self::YES;
                }

                //$content = json_decode(stripslashes(htmlspecialchars_decode($v['content'])));
                $content = $v['has_many_report_content'];
                foreach($content as &$vv){
                    !empty($vv['field_value']) && $v['cont'][] = ['id'=>$vv['id'], 'schedule_id'=>$vv['schedule_id'], 'name'=>$vv['field_name'], 'value'=>$vv['field_value']];
                }
                unset($v['content'],$v['select_user'],$v['read'],$v['deleted_at'], $v['has_many_report_content']);
            }
        }
        return $data;
    }


    /*
     * 汇报详情
     * */
    public function reportDetail($data, $user_id){
        try{
            throw_if(empty($data['id']), new Exception(ConstFile::API_PARAMETER_MISS));
            $info = $this->reportInfo($data['id']);
            throw_if(empty($info), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));

            $read_user = !empty($info['read']) ? array_filter($info['read']) : [];//已读用户
            $cc_user = !empty($info['cc_ids']) ? array_filter($info['cc_ids']) : [];//抄送人

            if(!empty($info['select_user']) && !in_array($user_id, $info['select_user']) && $user_id !== $info['user_id'] && !empty($cc_user) && !in_array($user_id, $cc_user)){
                return returnJson('您无权限查看该日志', ConstFile::API_RESPONSE_FAIL);
            }

            //进入详情更新已读数据
            if(in_array($user_id, $info['select_user']) && (empty($read_user) || !in_array($user_id, $read_user))){
                array_push($read_user, $user_id);
                //$read_user = collect($read_user)->concat([$user_id])->toArray();
                $res = Report::where('id', $info['id'])->update(['read'=> implode(',', $read_user)]);
                if(!$res){
                    return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
                }
            }
            $info['read'] = $read_user;
            $user = DepartUser::where(['user_id'=>$info['user_id'], 'is_primary'=>1])->with('user')->with('department')->first()->toArray();

            $select_user = count(array_filter($info['select_user']));
            $info['read_num'] = count($info['read']);
            $info['unread_num'] = $select_user - $info['read_num'];

            list($info['position'], $info['avatar'], $info['chinese_name'], $info['employee_num'],$info['department_name']) = [$user['user']['position'],$user['user']['avatar'],$user['user']['chinese_name'],$user['user']['employee_num'],$user['department']['name']];

            $comment = TotalComment::where([['type', 10],['relation_id', $data['id']]])->whereNull('total_comment.deleted_at')->count();
            $info['comment'] = $comment;
            $like = Like::where([['relate_id', $data['id']], ['user_id', $user_id]])->first();
            $info['is_like'] = !empty($like) ? 1 : 0;

            /*$ccIds = $info['cc_ids'];
            $ccUsers = User::query()->whereIn('id', $ccIds)->select(['id', 'chinese_name', 'avatar'])->get();
            $info['cc_user'] = $ccUsers;

            $schedules = Schedules::query()->with(['creator'])
                ->where('report_id', $data['id'])
                ->select(['content', 'start_at'])->get(); //'creator.chinese_name'
            $info['schedules'] = $schedules;*/
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$info);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 删除汇报
     * */
    public function delReport($data, $user){
        try{
            if (!$data['id']) {
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            }

            $info = Report::find($data['id'])->toArray();
            if(empty($info)){
                return returnJson(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            if($info['user_id'] != $user->id){
                return returnJson('不可以删除别人的汇报', ConstFile::API_RESPONSE_FAIL);
            }

            $res = Report::where('id', $info['id'])->delete();
            if(!$res){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 读汇报 TODO 删除接口
     * */
    public function readReport($data, $user){
        if (empty($data['id'])) {
            return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
        }

        $info = Report::where('id', $data['id'])->first();
        if(empty($info)){
            return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
        }
        $select_user = array_filter(explode(',', $info->select_user));
        if(!in_array($user->id, $select_user) && $user->id !== $info->user_id){
            return returnJson('您无权限查看该日志', ConstFile::API_RESPONSE_FAIL);
        }

        $read_user = array_filter(explode(',', $info->read));
        if(in_array($user->id, $select_user) && (empty($read_user) || !in_array($user->id, $read_user))){
            array_push($read_user, $user->id);
            $res = Report::find($info->id)->fill(['read'=> implode(',', $read_user)])->save();
            if(!$res){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
    }


    /*
     * 已读未读列表
     * */
    public function readList($data, $user){
        try{
            if (!$data['id']) {
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            }

            $info = Report::where('id', $data['id'])->select('id','user_id','select_user','read')->first();
            if(empty($info)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            $select_user = array_filter(explode(',', $info->select_user));
            $read = array_filter(explode(',', $info->read));
            $unread = array_diff($select_user, $read);//print_r($select_user);print_r($read);print_r($unread);die;

            $result = ['total'=>0, 'total_page'=>0, 'data'=>[]];
            $l_data = [];
            $is_remind = self::NO;//是否展示提醒按钮
            $list = User::select('id','name','chinese_name','avatar','position');
            if($data['type'] == 2){
                //未读人员
                if(!empty($unread)){
                    ($info->user_id == $user->id && count($unread)) && $is_remind = self::YES;
                    $l_data = $list->whereIn('id', $unread)->paginate($data['limit'])->toArray();
                }
            }else{
                //已读人员
                if(!empty($read)){
                    $l_data = $list->whereIn('id', $read)->paginate($data['limit'])->toArray();
                }
            }
            if(!empty($l_data)){
                $result['total'] = $l_data['total'];
                $result['total_page'] = $l_data['last_page'];
                $result['data'] = $l_data['data'];
            }
            $result['read_num'] = count($read);
            $result['unread_num'] = count($unread);
            $result['is_remind'] = $is_remind;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 未读列表提醒一下
     * */
    public function remindReader($param, $user){
        try{
            throw_if(empty($param['id']), new Exception('请选择汇报'));
            $report = Report::where('id', $param['id'])->first();
            throw_if(empty($report), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));

            $select_user = array_filter(explode(',', $report->select_user));
            $read = array_filter(explode(',', $report->read));
            $remind_user = array_diff($select_user, $read);

            //给未读人员发送消息
            if(!empty($remind_user)) {
                $data = [];
                foreach ($remind_user as $v) {
                    $data[] = [
                        'receiver_id' => $v,//接收者（申请人）
                        'sender_id' => $user->id,//发送者
                        'content' => $user->chinese_name . '提交的汇报，请查看',//内容
                        'type' => self::REPORT_MESSAGE_TYPE, //汇报消息
                        'relation_id' => $report->id,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];
                }
                if (!empty($data)) {
                    $res = Message::insert($data);
                    if (!$res) {
                        return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
                    }
                }
            }
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 我要写的汇报 TODO 优化
     * */
    public function myNeedReport($user){
        //选择我的规则
        $depart = DepartUser::where(['user_id'=>$user->id, 'is_primary'=>1])->value('department_id');//我所属的部门
        $uid = $user->id;
        $rule = ReportRule::leftJoin('report_templates', 'report_templates.id', '=', 'report_rules.report_type');

        $depart = collect($depart)->toArray();
        if(!empty($depart)){
            //选择了用户所属部门的规则也需要统计
            $rule->where(function ($query) use($uid, $depart){
                $query->whereRaw('find_in_set('.$uid.', report_rules.select_user)');
                if(count($depart) > 0){
                    $depart = array_unique(array_filter($depart));
                    foreach ($depart as $v){
                        $query->orWhereRaw('find_in_set('.$v.', report_rules.select_department)');
                    }
                }
            });
        }else{
            $rule->whereRaw('find_in_set('.$uid.', report_rules.select_user)');
        }
        $rule = $rule->select('report_rules.*', 'report_templates.template_name')
            ->get()->toArray();

        if(!empty($rule)){
            $template_id = array_unique(array_column($rule, 'report_type'));

            $report_info = Report::whereIn('template_id', $template_id)->where('user_id', $user->id)->orderBy('updated_at','asc')->select('updated_at','created_at','template_id')->get()->toArray();//我发布的汇报
            $report_info = array_field_as_key($report_info, 'template_id', ['template_id']);

            //判断每条规则，我所提交汇报的情况
            foreach ($rule as &$v){
                $v['chinese_name'] = $user->chinese_name;
                if (!empty($report_info[$v['report_type']])) {
                    $v['info'] = $this->userReportToRule($v, $report_info[$v['report_type']]['created_at']);//验证最新的汇报匹配规则的结果
                } else {
                    $v['info'] = $this->userReportToRule($v, 0);//统计汇报符合规则的情况
                }
                $all_sub = $this->checkReportSub($v);//统计规则中提交、未提交、迟交的人数
                $v['all_sub'] = ['is_unsub'=>count($all_sub['is_unsub']), 'is_sub'=>count($all_sub['is_sub']), 'is_later'=>count($all_sub['is_later'])];
            }
        }

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$rule);
    }


    /*
     * 汇报规则统计数据
     * */
    public function myReportRule($user){
        $data = ReportRule::where('report_rules.user_id',$user->id)
            ->leftJoin('report_templates', 'report_rules.report_type', '=', 'report_templates.id')
            ->select('report_rules.*', 'report_templates.template_name')
            ->get()->toArray();

        if(!empty($data)){
            foreach ($data as &$v){
                $res = $this->checkReportSub($v);//汇报规则统计
                $res['is_unsub'] =count($res['is_unsub']);
                $res['is_sub'] = count($res['is_sub']);
                $res['is_later'] = count($res['is_later']);

                unset($res['report']);
                $v = array_merge($v, $res);
            }
        }

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$data);
    }


    /*
     * 获取当前周期
     * */
    public function getCurrentCycle($param){
        try{
            if(empty($param['cycle'])){
                return returnJson('请汇报类型', ConstFile::API_RESPONSE_FAIL);
            }

            $date = time();
            $stime = $etime = '';

            $res = [];
            if($param['cycle'] == 3){
                //每月
                $tag = date('m', $date).'月';

                $stime = date('Y-m-d',mktime(0,0,0,date('m',$date),1,date('Y',$date)));
                $etime = date('Y-m-d',mktime(23,59,59,date('m',$date)+1,0,date('Y',$date)));
            }else if($param['cycle'] == 2){
                //每周
                $str1 = mktime(date('H'),date('i'),date('s'),date('m',$date),date('d', $date)-date('w', $date)+1,date('Y',$date));
                $str2 = mktime(date('H'),date('i'),date('s'),date('m',$date),date('d', $date)-date('w', $date)+7,date('Y',$date));
                $tag = date('Y年m月d日', $str1).'-'.date('Y年m月d日', $str2);

                $stime = date('Y-m-d', $str1);
                $etime = date('Y-m-d', $str2);
            }else{
                //每天
                $time_show = date('Y-m-d', $date);
                $tag = date('Y年m月d日', $date);

                $stime = date('Y-m-d', $date);
                $etime = date('Y-m-d', $date);
            }

            //$res['time_show'] = $time_show;
            $res['tag'] = $tag;
            $res['stime'] = $stime;
            $res['etime'] = $etime;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$res);

        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 获取上一个/下一个汇报时间点
     * */
    public function getPreOrAfterReportTime($info){
        try{
            $error = $this->checkPreAfterReportTime($info);//验证数据
            if ($error) {
                throw new Exception($error, ConstFile::API_RESPONSE_FAIL);
            }

            $time = time();
            $date = strtotime($info['current']);
            $stime = $etime = '';

            if($date > $time){
                return returnJson('不可以查看将来数据', ConstFile::API_RESPONSE_FAIL);
            }else{
                $res = [];
                if($info['cycle'] == self::CYCLE_MONTH){
                    //每月
                    if($info['tag'] == 'next'){
                        $time_show = date('Y-m-d', strtotime('+1 month', $date));
                    }else{
                        $time_show = date('Y-m-d', strtotime('-1 month', $date));
                    }
                    $tag = date('m', strtotime($time_show)).'月';

                    $stime = date('Y-m-d',mktime(0,0,0,date('m',strtotime($time_show)),1,date('Y',strtotime($time_show))));
                    $etime = date('Y-m-d',mktime(23,59,59,date('m',strtotime($time_show))+1,0,date('Y',strtotime($time_show))));
                }else if($info['cycle'] == self::CYCLE_WEEK){
                    //每周
                    if($info['tag'] == 'next'){
                        $time_show = date('Y-m-d', strtotime('+1 week', $date));
                    }else{
                        $time_show = date('Y-m-d', strtotime('-1 week', $date));
                    }

                    $tem = strtotime($time_show);
                    $str1 = mktime(date('H'),date('i'),date('s'),date('m',$tem),date('d', $tem)-date('w', $tem)+1,date('Y',$tem));
                    $str2 = mktime(date('H'),date('i'),date('s'),date('m',$tem),date('d', $tem)-date('w', $tem)+7,date('Y',$tem));
                    $tag = date('Y年m月d日', $str1).'-'.date('Y年m月d日', $str2);

                    $stime = date('Y-m-d', $str1);
                    $etime = date('Y-m-d', $str2);
                }else{
                    //每天
                    $rule = ReportRule::where('id', $info['rule_id'])->first();
                    if(empty($rule)){
                        return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
                    }

                    $cycle = explode(',', $rule->send_day_date);
                    $n_week = date('w', strtotime($info['current']));//星期
                    $index = array_search($n_week, $cycle);

                    if($info['tag'] == 'before') {
                        if (!empty($cycle[$index - 1])) {
                            $tem = $date - ($n_week - $cycle[$index - 1]) * 24 * 3600;
                        } else {
                            $last_index = $cycle[count($cycle) - 1];
                            $tem = mktime(date('H'), date('i'), date('s'), date('m',$date), date('d',$date) - date('w',$date) - 7 + $last_index, date('Y',$date));
                        }
                    }else{
                        if(!empty($cycle[$index+1])){
                            $tem = $date + ($cycle[$index+1] - $n_week)*24*3600;
                        }else{
                            $next_index = $cycle[0];
                            $tem = mktime(date('H'), date('i'), date('s'), date('m',$date),date('d',$date) - date('w',$date) + 7 + $next_index, date('Y',$date));
                        }
                    }

                    $time_show = date('Y-m-d', $tem);
                    $tag = date('Y年m月d日', $tem);
                    $stime = date('Y-m-d', $tem);
                    $etime = date('Y-m-d', $tem);
                }

                $res['time_show'] = $time_show;
                $res['tag'] = $tag;
                $res['stime'] = $stime;
                $res['etime'] = $etime;

                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$res);
            }
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 规则统计列表（包含我要写的，我发送的）
     * */
    public function ruleStatisticsList($info, $user){
        try {
            $res = $this->combinaCondition($info);;//验证数据

            if(!$res['status']){
                throw new Exception(sprintf($res['msg']), ConstFile::API_RESPONSE_FAIL);
            }

            $uid = $user->id;
            $rule = ReportRule::where('id', $info['rule_id'])->first();
            if(empty($rule)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            $report_rule = $this->checkReportSub(collect($rule)->toArray(),$res['data']);//汇报规则统计

            if($info['sub'] == 2){
                //迟交
                $users = $report_rule['is_later'];
            }else if($info['sub'] == 3){
                //未提交
                $users = $report_rule['is_unsub'];
            }else{
                //已提交
                $users = $report_rule['is_sub'];
            }

            $data = [];
            if(!empty($users)){
                $data = User::whereIn('id', $users)->select('id','employee_num','chinese_name','position','avatar')->get()->toArray();
                if(!empty($data)){
                    foreach ($data as &$v){
                        $is_lock = 0;//是否加锁
                        $id = 0;

                        //判断迟交和已提交列表中的汇报，是否需要加锁，查看人员非发送者和提交者加锁
                        if($info['sub'] !== 3 && !empty($report_rule['report'][$v['id']])){
                            $report = $report_rule['report'][$v['id']];
                            $select_user = explode(',', $report['select_user']);
                            if(!in_array($user->id, $select_user) && $user->id != $report['user_id']){
                                $is_lock = 1;
                            }
                            $id = $report['id'];
                        }
                        $v['report_id'] = $id;
                        $v['is_lock'] = $is_lock;
                    }
                }
            }

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,['is_later'=>count($report_rule['is_later']),'is_unsub'=>count($report_rule['is_unsub']),'is_sub'=>count($report_rule['is_sub']),'data'=>$data]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 日志报表 todo 待接入
     * */
    public function logReport($info){
        try {
            if(empty($info['template_id'])){
                return returnJson('请传模板id', ConstFile::API_RESPONSE_FAIL);
            }

            $where = [];
            if(!empty($info['stime'])){
                $where[] = ['updated_at', '>=', $info['stime']];
            }
            if(!empty($info['etime'])){
                $where[] = ['updated_at', '<=', $info['stime']];
            }

            $data = ReportTemplate::where('id', $info['template_id'])
                ->with(['report'=>function($query){
                    $query->with(['hasManyReportContent'=>function($query){
                        $query->select('id','report_id', 'field', 'field_name', 'field_type_value', 'field_value', 'required', 'schedule_id');
                    }]);
                }])
                ->with(['formField'=>function($query){
                    $query->select('id','template_id','field','field_name','field_type');
                }]);
            if(!empty($where)){
                $data->where($where);
            }
            $data = $data->first();
            if(empty($data)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }

            $uids = array_column(collect($data)->get('report'), 'user_id');
            $user = User::whereIn('id', $uids)->pluck('chinese_name','id')->toArray();

            $report = [];
            foreach (collect($data)->get('report') as $v) {
                $report[] = [
                    'user_name' => !empty($user[$v['user_id']]) ? $user[$v['user_id']] : '',
                    'add_time' => $v['updated_at'],
                    'content' => $v['has_many_report_content']
                    //'content' => json_decode(stripslashes(htmlspecialchars_decode($v['content'])))
                ];
            }
            $res = ['template_name'=>$data->template_name ,'form_field'=>collect($data)->get('form_field'), 'report'=>$report];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $res);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }


    /*
     * 查询日报提交信息
     * */
    public function checkReportSub($info, $condition=[]){
        $uids = array_filter(explode(',', $info['select_user']));//规则选择提交的人
        $depart = array_filter(explode(',', $info['select_department']));//选择提交的部门，部门会有新人增加，所以直接重新查询

        if(!empty($depart)){
            $depart_user = DepartUser::getDepartUsers($depart)->get()->toArray();
            $depart_user_ids = collect($depart_user)->pluck('user_id')->toArray();
            $depart_user_ids = array_filter($depart_user_ids);//print_r($depart_user_ids);die;
            !empty($depart_user_ids) && $uids = array_unique(array_merge($uids, $depart_user_ids));
        }

        //选择提交的员工
        $count = count($uids);
        $user = User::whereIn('id', $uids)->pluck('chinese_name')->take(2)->toArray();
        $is_unsub = $is_sub = $is_later = $report = [];

        if(!empty($uids)){
            foreach ($uids as $v){
                $report_info = Report::where([['template_id', $info['report_type']],['user_id', $v]])->orderBy('updated_at', 'desc')->take(1)->select('id', 'user_id', 'template_id','created_at','updated_at','select_user');//所选会员最新提交的汇报

                if(!empty($condition)){
                    $report_info->where($condition);
                }
                $report_info = $report_info->first();

                if(!empty($report_info)){
                    $tem = $this->userReportToRule($info, collect($report_info)->get('created_at'));
                    if($tem['sub'] == 0 || $tem['sub'] == -1){
                        $is_unsub[] = $v;
                    }
                    if($tem['sub'] == 1){
                        $is_sub[] = $v;
                    }
                    if($tem['sub'] == 2){
                        $is_later[] = $v;
                    }
                }else{
                    $is_unsub[] = $v;
                }
                $report[$v] = collect($report_info)->toArray();
            }
        }

        return ['count'=>$count, 'user'=>$user, 'is_unsub'=>$is_unsub, 'is_sub'=>$is_sub, 'is_later'=>$is_later, 'report'=>$report];
    }


    /*
     * 计算员工汇报对应统计规则情况
     * */
    public function userReportToRule($rule, $report_time){
        //汇报周期
        $time = time();
        $n_week = date('w');//星期
        $date = strtotime($report_time);

        $sub = $last = $over = 0;
        switch ($rule['send_cycle']){
            case self::CYCLE_DAY:
                //每天
                $cycle = explode(',', $rule['send_day_date']);
                $index = array_search($n_week, $cycle);

                if($index !== false){
                    //今天属于应汇报的日期
                    $is_in = 1;
                }else{
                    //今天不属于汇报的日期
                    array_push($cycle, $n_week);
                    sort($cycle);
                    $index = array_search($n_week, $cycle);
                    $is_in = 0;
                }

                //汇报时间,每次规则按照24小时计算
                if($rule['stime'] >= $rule['etime']){
                    //隔天
                    $is_cross = 1;
                }else{
                    //当天
                    $is_cross = 0;
                }
                $res_day = $this->calculateRuleTimeByDay($is_cross, $is_in, $index, $cycle, $rule);//返回规则周期

                //汇报时间,每次规则按照24小时计算
                $result = $this->onRuleTimeShow($time, $date, $res_day);
                break;
            case self::CYCLE_WEEK:
                //每周
                $cycle = explode(',', $rule['send_week_date']);

                //汇报时间,每次规则按照24小时计算
                if(($cycle[0] < $cycle[1]) || ($cycle[0] == $cycle[1] && $rule['stime'] < $rule['etime'])){
                    //周期到本周
                    $is_cross = 0;
                }else{
                    //周期到下周
                    $is_cross = 1;
                }
                $res_day = $this->calculateRuleTimeByWeek($is_cross, $cycle, $rule);//返回规则周期

                //汇报时间,每次规则按照24小时计算
                $result = $this->onRuleTimeShow($time, $date, $res_day);
                break;
            case self::CYCLE_MONTH:
                //每月
                $cycle = explode(',', $rule['send_month_date']);

                //汇报时间,每次规则按照24小时计算
                if(($cycle[0] < $cycle[1]) || ($cycle[0] == $cycle[1] && $rule['stime'] < $rule['etime'])){
                    //周期到本月
                    $is_cross = 0;
                }else{
                    //周期到下月
                    $is_cross = 1;
                }
                $res_day = $this->calculateRuleTimeByMonth($is_cross, $cycle, $rule);//返回规则周期

                //汇报时间,每次规则按照24小时计算
                $result = $this->onRuleTimeShow($time, $date, $res_day);
                break;
            default:
                break;
        }

        $sub = $result['sub'];
        $last = $result['last'];
        $over = $result['over'];

        if($sub == -1){
            $tag = '还有'.time2string($last, ['day'=>'天', 'hour'=>'小时','minute'=>'分']).'截止';
        }else if($sub == 1){
            $tag = date('H:i:s', $date).' 提交';
        }else if($sub == 2){
            $tag = date('H:i:s', $date).' 迟交';
        }else{
            $tem = time2string($over, ['day'=>'天', 'hour'=>'小时']);
            $tag = '超过'.$tem.'未提交';
        }

        return ['sub'=>$sub, 'tag'=>$tag];
    }


    /*
     * 计算统计周期为天的上下次统计时间
     * param $is_cross 是否隔天
     * param $is_in 是否属于统计周期中
     * param $index 在统计周期中的位移
     * param $cycle 统计周期值
     * param $rule 规则信息
     * */
    public function calculateRuleTimeByDay($is_cross, $is_in, $index, $cycle, $rule){
        $time = time();
        $n_week = date('w');

        if(!empty($cycle[$index-1])){
            $last_day = $time - ($n_week - $cycle[$index-1])*24*3600;
        }else{
            $last_index = $cycle[count($cycle)-1];
            $last_day = mktime(date('H'),date('i'),date('s'),date('m'),date('d')-date('w')-7+$last_index,date('Y'));
        }

        if(!empty($cycle[$index+1])){
            $next_day = $time + ($cycle[$index+1] - $n_week)*24*3600;
        }else{
            $next_index = $cycle[0];
            $next_day = mktime(date('H'),date('i'),date('s'),date('m'),date('d')-date('w')+7+$next_index,date('Y'));
        }

        $now_stime = $now_etime = $next_stime = $next_etime = '';

        if($is_in){
            if($is_cross){
                //隔天
                //上次的规则
                $last_stime = strtotime(date("Y-m-d", $last_day).' '.$rule['stime']);
                $last_etime = strtotime(date('Y-m-d', $last_day+24*3600).' '.$rule['etime']);

                //当前的规则
                $now_stime = strtotime(date('Y-m-d', $time).' '.$rule['stime']);
                $now_etime = strtotime(date("Y-m-d",strtotime("+1 day")).' '.$rule['etime']);
            }else{
                //当天
                $last_stime = strtotime(date("Y-m-d", $last_day).' '.$rule['stime']);
                $last_etime = strtotime(date('Y-m-d', $last_day).' '.$rule['etime']);

                $now_stime = strtotime(date("Y-m-d", $time).' '.$rule['stime']);
                $now_etime = strtotime(date("Y-m-d", $time).' '.$rule['etime']);

                $next_stime = strtotime(date("Y-m-d", $next_day).' '.$rule['stime']);
                $next_etime = strtotime(date("Y-m-d", $next_day).' '.$rule['etime']);
            }
        }else{
            if($is_cross){
                //隔天
                //上次的规则
                $last_stime = strtotime(date("Y-m-d", $last_day).' '.$rule['stime']);
                $last_etime = strtotime(date('Y-m-d', $last_day+24*3600).' '.$rule['etime']);

                //下次的规则
                $next_stime = strtotime(date('Y-m-d', $next_day).' '.$rule['stime']);
                $next_etime = strtotime(date("Y-m-d",$next_day+24*3600).' '.$rule['etime']);
            }else{
                //当天
                $last_stime = strtotime(date("Y-m-d", $last_day).' '.$rule['stime']);
                $last_etime = strtotime(date('Y-m-d', $last_day).' '.$rule['etime']);

                $next_stime = strtotime(date("Y-m-d", $next_day).' '.$rule['stime']);
                $next_etime = strtotime(date("Y-m-d", $next_day).' '.$rule['etime']);
            }
        }

        return ['last_stime'=>$last_stime, 'last_etime'=>$last_etime, 'now_stime'=>$now_stime, 'now_etime'=>$now_etime, 'next_stime'=>$next_stime, 'next_etime'=>$next_etime];
    }


    /*
     * 计算统计周期为周的上下次统计时间
     * param $is_cross 是否隔周或月
     * param $is_in 是否属于统计周期中
     * param $index 在统计周期中的位移
     * param $cycle 统计周期值
     * param $rule 规则信息
     * */
    public function calculateRuleTimeByWeek($is_cross, $cycle, $rule){
        $now_stime = $now_etime = $next_stime = $next_etime = '';

        if($is_cross){
            //周期到下周
            if(date('d')-date('w') < 0){
                $last_sday = mktime(date('H'), date('i'), date('s'), date('m'), - 7 + $cycle[0], date('Y'));
                $last_eday = mktime(date('H'), date('i'), date('s'), date('m'), $cycle[1], date('Y'));
            }else{
                $last_sday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') - 7 + $cycle[0], date('Y'));
                $last_eday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') + $cycle[1], date('Y'));
            }

            $next_sday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') + $cycle[0], date('Y'));
            $next_eday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') + 7 + $cycle[1], date('Y'));

        }else{
            //本周
            $last_sday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') - 7 + $cycle[0], date('Y'));
            $last_eday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') - 7 + $cycle[1], date('Y'));

            $now_sday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') + $cycle[0], date('Y'));
            $now_eday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') + $cycle[1], date('Y'));

            $next_sday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') + 7 + $cycle[0], date('Y'));
            $next_eday = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') + 7 + $cycle[1], date('Y'));

            //本次的规则
            $now_stime = strtotime(date("Y-m-d", $now_sday).' '.$rule['stime']);
            $now_etime = strtotime(date('Y-m-d', $now_eday).' '.$rule['etime']);
        }

        //上次的规则
        $last_stime = strtotime(date("Y-m-d", $last_sday).' '.$rule['stime']);
        $last_etime = strtotime(date('Y-m-d', $last_eday).' '.$rule['etime']);

        //下次的规则
        $next_stime = strtotime(date('Y-m-d', $next_sday).' '.$rule['stime']);
        $next_etime = strtotime(date("Y-m-d", $next_eday).' '.$rule['etime']);

        return ['last_stime'=>$last_stime, 'last_etime'=>$last_etime, 'now_stime'=>$now_stime, 'now_etime'=>$now_etime, 'next_stime'=>$next_stime, 'next_etime'=>$next_etime];
    }


    /*
     * 计算统计周期为月的上下次统计时间
     * param $is_cross 是否隔周或月
     * param $is_in 是否属于统计周期中
     * param $index 在统计周期中的位移
     * param $cycle 统计周期值
     * param $rule 规则信息
     * */
    public function calculateRuleTimeByMonth($is_cross, $cycle, $rule){
        $now_stime = $now_etime = $next_stime = $next_etime = '';

        if($is_cross){
            //周期到下月
            if(date('m') == 1){
                $last_sday = mktime(date('H'), date('i'), date('s'), 12, $cycle[0], date('Y')-1);
            }else{
                $last_sday = mktime(date('H'), date('i'), date('s'), date('m')-1, $cycle[0], date('Y'));
            }
            $last_eday = mktime(date('H'), date('i'), date('s'), date('m'), $cycle[1], date('Y'));

            $next_sday = mktime(date('H'), date('i'), date('s'), date('m'), $cycle[0], date('Y'));
            if(date('m') == 12){
                $next_eday = mktime(date('H'), date('i'), date('s'), 1, $cycle[1], date('Y')+1);
            }else{
                $next_eday = mktime(date('H'), date('i'), date('s'), date('m')+1, $cycle[1], date('Y'));
            }
        }else{
            //本月
            if(date('m') == 1){
                $last_sday = mktime(date('H'), date('i'), date('s'), 12, $cycle[0], date('Y')-1);
                $last_eday = mktime(date('H'), date('i'), date('s'), 12, $cycle[1], date('Y')-1);
            }else{
                $last_sday = mktime(date('H'), date('i'), date('s'), date('m')-1, $cycle[0], date('Y'));
                $last_eday = mktime(date('H'), date('i'), date('s'), date('m')-1, $cycle[1], date('Y'));
            }

            $now_sday = mktime(date('H'), date('i'), date('s'), date('m'), $cycle[0], date('Y'));
            $now_eday = mktime(date('H'), date('i'), date('s'), date('m'), $cycle[1], date('Y'));

            if(date('m') == 12){
                $next_sday = mktime(date('H'), date('i'), date('s'), 1, $cycle[0], date('Y')+1);
                $next_eday = mktime(date('H'), date('i'), date('s'), 1, $cycle[1], date('Y')+1);
            }else{
                $next_sday = mktime(date('H'), date('i'), date('s'), date('m')+1, $cycle[0], date('Y'));
                $next_eday = mktime(date('H'), date('i'), date('s'), date('m')+1, $cycle[1], date('Y'));
            }

            //本次的规则
            $now_stime = strtotime(date("Y-m-d", $now_sday).' '.$rule['stime']);
            $now_etime = strtotime(date('Y-m-d', $now_eday).' '.$rule['etime']);
        }

        //上次的规则
        $last_stime = strtotime(date("Y-m-d", $last_sday).' '.$rule['stime']);
        $last_etime = strtotime(date('Y-m-d', $last_eday).' '.$rule['etime']);

        //下次的规则
        $next_stime = strtotime(date('Y-m-d', $next_sday).' '.$rule['stime']);
        $next_etime = strtotime(date("Y-m-d", $next_eday).' '.$rule['etime']);

        return ['last_stime'=>$last_stime, 'last_etime'=>$last_etime, 'now_stime'=>$now_stime, 'now_etime'=>$now_etime, 'next_stime'=>$next_stime, 'next_etime'=>$next_etime];
    }


    /*
     * 规则所在区间显示提交状态
     * */
    public function onRuleTimeShow($time, $date, $day){
        $sub = $last = $over = 0;

        //上次的规则
        $last_stime = $day['last_stime'];
        $last_etime = $day['last_etime'];

        //当前的规则
        $now_stime = $day['now_stime'];
        $now_etime = $day['now_etime'];

        //下次规则
        $next_stime = $day['next_stime'];
        $next_etime = $day['next_etime'];

        //print_r($day);die;
        //print_r(date('Y-m-d H:i:s',$time));print_r(date('Y-m-d',$last_stime));print_r(date('Y-m-d',$last_etime));print_r(date('Y-m-d',$next_stime));print_r(date('Y-m-d',$next_etime));die;

        if($last_stime && $last_etime && $time >= $last_stime && $time <= $last_etime){//echo 1;die;
            //当前时间在上次周期中
            if($date < $last_stime){
                //$sub = 0;
                $sub = -1;
                $last = $last_etime - $time;
            }else if($date >= $last_stime && $date <= $time){
                $sub = 1;
            }
        }else if($now_stime && $now_etime && $time >= $now_stime && $time <= $now_etime){//echo 2;die;
            //本次周期中
            if($date < $now_stime){
                //$sub = 0;
                $sub = -1;
                $last = $now_etime - $time;
            }else if($date >= $now_stime && $date <= $time){
                $sub = 1;
            }
        }else if($next_stime && $next_etime && $time>= $next_stime && $time <= $next_etime){//echo 3;die;
            //当前时间在下次周期中
            if($date < $next_stime){
                //$sub = 0;
                $sub = -1;
                $last = $next_etime - $time;
            }else if($date >= $next_stime && $date <= $time){
                $sub = 1;
            }
        }else if($last_etime && $now_stime && $time > $last_etime && $time < $now_stime){//echo 4;die;
            //本次与上次周期之间
            if($date < $last_stime){
                $sub = 0;
                $over = $time - $last_etime;//超过时间
            }else if($date >= $last_stime && $date <= $last_etime){
                $sub = 1;
            }else if($date > $last_etime && $date <= $time){
                $sub = 2;
            }
        }else if($now_etime && $next_stime && $time > $now_etime && $time < $next_stime){//echo 5;die;
            //本次与下次周期之间
            if($date < $now_stime){
                $sub = 0;
                $over = $time - $now_etime;//超过时间
            }else if($date >= $now_stime && $date <= $now_etime){
                $sub = 1;
            }else if($date > $now_etime && $date < $time){
                $sub = 2;
            }
        }else if($last_etime && $next_stime && $time > $last_etime && $time < $next_stime){//echo 6;die;
            //当前时间在上下两次周期中间
            if($date < $last_stime){
                $sub = 0;
                $over = $time - $last_etime;//超过时间
            }else if($date >= $last_stime && $date <= $last_etime){
                $sub = 1;
            }else if($date > $last_etime && $date < $next_stime){
                $sub = 2;
            }
        }
        return ['sub'=>$sub, 'last'=>$last, 'over'=>$over];
    }


    /*
     * 检测输入数据
     * */
    private function checkData($data){
        if (empty($data)) {
            return '请求数据不能为空';
        }

        if (empty($data['report_type'])) {
            return '请选择汇报类型';
        }

        if (empty($data['worker']) && empty($data['depart'])) {
            return '请选择需要提交的员工';
        }

        if (empty($data['send_cycle'])) {
            return '请选择提交周期';
        }

        if($data['send_cycle'] == 1 && empty($data['send_day_date'])){
            return '请选择提交日期';
        }
        if($data['send_cycle'] == 2 && empty($data['send_week_date'])){
            return '请选择提交日期';
        }
        if($data['send_cycle'] == 3 && empty($data['send_month_date'])){
            return '请选择提交日期';
        }

        if(empty($data['stime'])){
            return '请选择开始时间';
        }

        if(empty($data['etime'])){
            return '请选择结束时间';
        }

        return '';
    }


    /*
     * 检测模板输入数据
     * */
    private function checkTempData($data){
        if (empty($data)) {
            return '请求数据不能为空';
        }
        if(empty($data['template_name'])){
            return '请填写模板名称';
        }

        $field = htmlspecialchars_decode($data['field']);
        $field = json_decode($field,true);
        if(empty($field)){
            return '请填写模板内容';
        }
        return '';
    }


    /*
     * 检测汇报输入数据
     * */
    private function checkReportData($data){
        if (empty($data)) {
            return '请求数据不能为空';
        }

        if(empty($data['template_id'])){
            return '请选择填写的模板';
        }

        if(empty($data['select_user'])){
            return '请选择接收人';
        }
        $select_user = array_filter(explode(',', $data['select_user']));
        if(empty($select_user)){
            return '请选择接收人';
        }

        $content = htmlspecialchars_decode($data['content']);
        $content = json_decode($content,true);
        if(empty($content) || !is_array($content)){
            return '请填写汇报内容';
        }

        foreach ($content as $v){
            if($v['required'] && empty($v['field_value'])){
                return '请填写'.$v['field_name'];
            }
            if(!empty($v['field_value']) && $v['field_type_value'] == 'number' && !is_numeric($v['field_value'])){
                return $v['field_name'].'请填写数字';
            }
        }

        return '';
    }


    /*
     * 组合查询条件
     * */
    private function combinaCondition($info){
        $msg = '';

        if(empty($info['rule_id'])){
            $msg = '请传汇报规则id';
        }
        if(empty($info['sub'])){
            $msg = '请选择提交选项';
        }
        if(empty($info['stime'])){
            $msg = '请选择筛选开始时间';
        }
        if(empty($info['etime'])){
            $msg = '请选择筛选结束时间';
        }

        if($msg){
            return ['status'=>0,'msg'=>$msg];
        }

        $stime = date('Y-m-d', strtotime($info['stime'])).' 00';
        $etime = date('Y-m-d', strtotime($info['etime'])).' 23:59:59';
        $where = [['updated_at','>=',$stime],['updated_at','<=',$etime]];

        return ['status'=>1,'data'=>$where];
    }


    /*
     * 汇报规则数据填充
     * */
    private function fillReportRuleData($data, $user){
        //选择部门
        $depart = !empty($data['depart']) ? array_filter($data['depart']) : [];
        $select_user = !empty($data['worker']) ? array_filter($data['worker']) : [];

        //1.修改规则
        $da = [
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'report_type' => $data['report_type'],
            'send_cycle' => $data['send_cycle'],
            'send_day_date' => empty($data['send_day_date']) ? '' : $data['send_day_date'],
            'send_week_date' => empty($data['send_week_date']) ? '' : $data['send_week_date'],
            'send_month_date' => empty($data['send_month_date']) ? '' : $data['send_month_date'],
            'stime' => $data['stime'],
            'etime' => $data['etime'],
            'is_legal_day_send' => $data['is_legal_day_send'],
            'is_remind' => $data['is_remind'],
            'remind_time' => $data['remind_time'],
            'remind_content' => $data['remind_content'],
            'select_user' => implode(',', $select_user),//选择的员工
            'select_department' => implode(',', $depart),//选择的部门
            'receive_level' => intval($data['receive_level']) //选择接收者主管等级
        ];

        return $da;
    }


    /*
     * 汇报列表数据验证
     * */
    private function checkReportListData($data){
        $where = [];
        $tag = '';
        if(!empty($data['template_id'])){
            $where[] = ['reports.template_id', $data['template_id']];//模板
            $template = ReportTemplate::where('id', $data['template_id'])->pluck('template_name')->first();
            if(!empty($template)){
                $tag = $template;
            }
        }
        if(!empty($data['stime'])){
            $where[] = ['reports.created_at', '>=', $data['stime']];
        }
        if(!empty($data['etime'])){
            $where[] = ['reports.created_at', '<=', $data['etime']];
        }
        return ['where'=>$where, 'tag'=>$tag];
    }


    /*
     * 上下时间点检测
     * */
    private function checkPreAfterReportTime($info){
        $msg = '';

        if(empty($info['rule_id'])){
            $msg ='请传汇报规则id';
        }
        if(empty($info['cycle']) || !in_array($info['cycle'], [1,2,3])){
            $msg = '请传汇报周期';
        }
        if(empty($info['current'])){
            $msg = '请传当前统计时间';
        }
        if(!in_array($info['tag'], array('next','before'))){
            $msg = '请选择上下时间';
        }

        return $msg;
    }


    /*批量更新*/
    public function updateBatch($tableName = "", $multipleData = array()){
        if( $tableName && !empty($multipleData) ) {
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";

            return DB::update(DB::raw($q));
        } else {
            return false;
        }
    }
}