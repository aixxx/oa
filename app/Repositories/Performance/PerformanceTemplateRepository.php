<?php

namespace App\Repositories\Performance;

use App\Models\Message\Message;
use App\Models\Performance\PerformanceApplication;
use App\Models\Performance\PerformanceApplicationSon;
use App\Models\Performance\PerformanceTemplate;
use App\Models\Performance\PerformanceTemplateContent;
use App\Models\Performance\PerformanceTemplateQuota;
use App\Models\Performance\PerformanceTemplateSon;
use Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Constant\ConstFile;
use App\Models\User;
use Exception;
use Request;
use DB;


/**
 * Class UsersRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PerformanceTemplateRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PerformanceTemplate::class;
    }

    /*
     * 4-16 添加绩效模板 改版
     * gaolu
     */
    public function setAddTow($uid,$arr) {
        //return returnJson($message='请输入正确内容',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$arr);
        if(empty($arr['title'])){
            return returnJson($message='模板名称不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['type_id'])){
            return returnJson($message='员工类型不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['department_id'])){
            return returnJson($message='员工部门不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['object_id'])){
            return returnJson($message='考核对象不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
//        if(empty($arr['money'])){
//            return returnJson($message='绩效金额不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
//        }
        if(empty($arr['review_time'])){
            return returnJson($message='自评时间不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['remind_time'])){
            return returnJson($message='自评提醒时间不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $sum=0;
        //var_dump($arr['content']);
        foreach ($arr['name'] as $key=>$values){
            if(!$values){
                return returnJson($message='指标维度名称不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($arr['approval_id'][$key])){
                return returnJson($message='指定考核人不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $array = $arr['content'][$key];

            $sum1=0;
            foreach ($array as $keys=>$ls){
                $number=count($ls);
                if($number!=4){//指标的4项 都必须填写
                    return returnJson($message='指标项不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
                }
                $sum = $sum + $ls[2];
                $sum1 = $sum1 + $ls[2];
            }
            $arr['num'][$key]=$sum1;
        }

        $sumOne = 1;
        $sumZero =0;
        $sumNinety = 99;
        $sumHundred = 100;
        if($sum>$sumHundred){
            return returnJson($message='总权重最大为100!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['setup'])){
            return returnJson($message='奖惩设置不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        foreach ($arr['setup'] as $key=>$va){
            if(intval($va['start'])<$sumZero){
                return returnJson($message='最低值参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            if(intval($va['start'])>$sumNinety){
                return returnJson($message='最低值参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            if(intval($va['end'])<$sumOne){
                return returnJson($message='最高值参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            if(intval($va['end'])>$sumHundred){
                return returnJson($message='最低值参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }
        $data['company_id']=$arr['company_id'];
        $data['title']=trim($arr['title']);//员工类型
        $data['type_id']=intval($arr['type_id']);//员工类型
        $data['department_id']=intval($arr['department_id']);//员工部门
        $data['object']=trim($arr['object_id']);//考核对象
        $data['review_time']=intval($arr['review_time']);//自评时间
        $data['remind_time']=intval($arr['remind_time']);//自评提醒时间
        //$data['money']=intval($arr['money']);//绩效金额
        $data['number']=$sum;//总权重
        $data['user_id']=$uid;//添加人id
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['frequency'] = count($arr['name']);
        try{
            DB::transaction(function() use($data,$arr) {
                $PerformanceTemplateid = PerformanceTemplate::insertGetId($data);//添加模板
                if ($PerformanceTemplateid) {

                    foreach ($arr['name'] as $key => $valuesone) {
                        $data = array();
                        $data['approval_id'] = $arr['approval_id'][$key];
                        $data['title'] = $valuesone;
                        $data['numb'] = $arr['num'][$key];
                        $data['pt_id'] = $PerformanceTemplateid;
                        $data['created_at'] = date('Y-m-d H:i:s', time());
                        $array = $arr['content'][$key];;//指标转数组

                        $sonid = PerformanceTemplateSon::insertGetId($data);//添加维度
                        if ($sonid) {
                            foreach ($array as $keys => $ls) {
                                $datas['title'] = $ls[0];
                                $datas['standard'] = $ls[1];
                                $datas['weight'] = $ls[2];
                                $datas['value'] = $ls[3];
                                $datas['pts_id'] = $sonid;
                                $datas['created_at'] = date('Y-m-d H:i:s', time());
                                $list[] = $datas;
                            }
                        }

                    }
                    PerformanceTemplateQuota::insert($list);//添加指标

                    foreach ($arr['setup'] as $key=>&$va){
                        $va['pt_id'] = $PerformanceTemplateid;
                        $va['created_at'] = date('Y-m-d H:i:s', time());
                    }
                    PerformanceTemplateContent::insert($arr['setup']);//添加绩效奖惩金额
                }
            });
            return returnJson($message = '添加成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($message='添加失败',$code=ConstFile::API_RESPONSE_FAIL);
        }

    }
    /*
     * 4-16 绩效模板详情
     * gaolu
     */
    public function getInfoOne($uid,$id) {
        $where['a.id']=intval($id);
        $info = DB::table('performance_template as a')
            ->leftJoin('departments as b','a.department_id','=','b.id')
            ->where($where)
            ->first(['a.title','a.type_id','a.department_id','a.object','a.review_time','a.remind_time','a.money','a.number','a.usage_number','b.name']);
        if($info){
            $wheres['a.pt_id']=intval($id);
            $lists = DB::table('performance_template_son as a')
                    ->leftJoin('users as b','a.approval_id','=','b.id')
                    ->where($wheres)
                    ->get(['a.id','a.title','a.numb','b.avatar','b.chinese_name'])->toArray();

            foreach ($lists as $key=>&$value){
                $wheress['pts_id']=$value->id;
                $listsr = DB::table('performance_template_quota')->where($wheress)->get(['title','standard','weight','value'])->toArray();
                $value->list=$listsr;
            }

            $conten = PerformanceTemplateContent::where('pt_id',intval($id))->get(['id','start','end','value','type']);
            $info->conten=[];
            if($conten){
                $info->conten=$conten->toArray();
            }
            if($lists){
                $info->list=$lists;
            }
        }
        if($info){
            $info->type_name=ConstFile::$staffTypeList[$info->type_id];//员工类型
            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
        }
        return returnJson($message='数据不存在',$code=ConstFile::API_RESPONSE_FAIL);
    }
    /*
     * 4-16 绩效模板列表
     * gaolu
     */
    public function getList($company_id,$arr) {
        $where[]=['company_id',$company_id];
        if(!empty($arr['status'])){
            $where[]=['status',PerformanceTemplate::API_STATUS_SUCCESS];
        }
        if(!empty($arr['type_id'])){
            $where[]=['type_id',intval($arr['type_id'])];
        }
        if(!empty($arr['department_id'])){
            $where[]=['department_id',intval($arr['department_id'])];
        }
        if(!empty($arr['object'])){
            $object=trim($arr['object']);
            $where[]=['object','like','%'.$object.'%'];
        }
        $lists=PerformanceTemplate::where($where)->get(['id','title','object','usage_number']);

        if($lists){
            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$lists);
        }
        return returnJson($message='请添加绩效模板！',$code=ConstFile::API_RESPONSE_FAIL);
    }
    /*
     * 4-16 考核对象数据列表
     * gaolu
     */
    public function getObjectList() {
        $lists=[];
        $list=User::groupBy('position')->get(['position']);
        if($list){
            $lists=$list->toArray();
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$lists);
    }
    /*
     * 4-16 用户绑定模板id
     * gaolu
     */
    public function setUpdate($user_id,$id) {

        $user_id=intval($user_id);
        $id=intval($id);
        $where['id']=$id;
        $info=PerformanceTemplate::where($where)->first(['usage_number','userarr']);
        if($info){
            $data['usage_number']=$info->usage_number+1;
            if($info->usage_number){
                $data['userarr']=$info->userarr.','.$user_id;
            }else{
                $data['userarr']=$user_id;
            }
            $n = PerformanceTemplate::where($where)->update($data);
            return $n;
        }
        return false;
    }
    /*
     * 4-16 参与绩效考核人列表
     * gaolu
     */
    public function getUserList($company_id,$arr) {
        $id=intval($arr['id']);
        $where['id']=$id;
        $company_id=intval($company_id);
        $where['company_id']=$company_id;
        $userarr=PerformanceTemplate::where($where)->value('userarr');
        $userids=explode(',',$userarr);
        if($userarr){
            $wheres[]=['id','in',$userarr];
            $list = user::with('getDepartmentID.getPrimaryDepartmentA')
                    ->whereIn('id',$userids)
                    ->get(['avatar','join_at','chinese_name','position','id']);
            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$list);
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$list=[]);
    }

    /*
     * 4-17 给所有人发绩效申请
     * gaolu
     */
    public function setBatchApply() {
        $day=date('d',time());
        $where['remind_time']=$day;
        $list=PerformanceTemplate::where($where)->get(['userarr','id','frequency','money'])->toArray();

        foreach ($list as $key=> $value){
            $id=$value['id'];
            $useridArr=explode(',',$value['userarr']);
            $frequency=$value['frequency'];
            foreach ($useridArr as $val){
                //给用户发生绩效申请
                $this->setAchievements($val,$id,$frequency,$value['money']);
            }
        }
        echo 1;
    }

    /*
     * 4-17 给所有人发绩效申请
     * gaolu
     */
    public function  setAchievements($user_id,$ptid,$frequency,$money){
        $date=date('Y年m月',time());
        $title=$date.'绩效考核';
        $number=date('YmdHis',time()).$user_id.rand(1000,9999);
        $view_password=$this->random_code(6);
        $amonth=date('Y-m',time());

        $where['pt_id']=$ptid;
        $where['amonth']=$amonth;
        $where['user_id']=$user_id;

        $n = PerformanceApplication::where($where)->value('id');
        if($n){
            return false;
        }
        $data['title']=$title;
        $data['user_id']=$user_id;
        $data['pt_id']=$ptid;
        $data['amonth']=$amonth;
        //$data['number']=$number;
        $data['view_password']=$view_password;
        $data['audit_times']=$frequency;
        $data['money']=$money;
        $data['created_at']=date('Y-m-d H:i:s',time());
        $n=PerformanceApplication::insertGetId($data);//生成绩效申请
        if($n){
            //给用户发放 绩效生成提醒并且给出查看密码
            $data = [
                'receiver_id' => $user_id,//接收者（申请人）
                'sender_id' => 0,//发送者（最后审批人）
                'content'=> $title.'需自评',//内容
                'type' => Message::MESSAGE_TYPE_ACHIEVEMENTS,
                'relation_id' => $n,//workflow_entries 的 id
                'created_at'=>date('Y-m-d H:i:s',time()),
                'updated_at'=>date('Y-m-d H:i:s',time())
            ];
            $datas[]=$data;
            Message::insert($datas);
            return true;
        }
        return false;
    }


    /*
     * 4-17 生成查看密码
     * gaolu
     */
    public function random_code($length = 8,$chars = null){
        if(empty($chars)){
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }
        $count = strlen($chars) - 1;
        $code = '';
        while( strlen($code) < $length){
            $code .= substr($chars,rand(0,$count),1);
        }
        return $code;
    }

    /*
     * 4-17    被审核人的绩效申请列表
     * gaolu
     */
    public function getMeApplyList($user_id,$perpage=10){
        $where['user_id']=$user_id;
        $list=PerformanceApplication::where($where)->orderBy('id','desc')
              ->select(['id','title','view_password','result','is_status','status'])->paginate($perpage);
        if($list){
            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$list);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }

    /*
     * 4-17    离职清除绩效模板中绑定的用户id
     * gaolu
     */
    public function setQuitTemplate($user_id=0,$pt_id=0){
        $pt_id=0;// 修要修改
        $where['id']=$pt_id;
        $info = PerformanceTemplate::where($where)->first(['usage_number','userarr'])->toArray();
        if($info){
              $data['usage_number'] = $info['usage_number'] -1;
              $userArr = explode(',',$info['userarr']);
              $userArr = array_diff($userArr,[$user_id]);
              $userStr = implode(',',$userArr);
              $data['userarr'] = $userStr;
              $n = PerformanceTemplate::where($where)->update($data);
              if($n){
                  return true;
              }
        }
        return false;

    }


    /*
     * 4-17    绩效申请详情--自评页面
     * gaolu
     */
    public function getMeApplyInfo($user_id,$arr){
        //$where['view_password']=trim($arr['password']);
        $where['id']=intval($arr['id']);
        $where['user_id']=$user_id;
        $where['status']=PerformanceApplication::API_STATUS_UNEVALUATED;
        $info=PerformanceApplication::where($where)->first(['id','title','pt_id','is_status','status']);//->toArray();
        //var_dump($info);//die;
        if($info){
            $wheres['a.pt_id']=$info->pt_id;
            $lists = DB::table('performance_template_son as a')
                ->leftJoin('users as b','a.approval_id','=','b.id')
                ->where($wheres)
                ->get(['a.id','a.title','a.numb','approval_id','b.avatar','b.chinese_name'])->toArray();
            //var_dump($lists);
            foreach ($lists as $key=>&$value){
                $wheress['pts_id']=$value->id;
                $listsr = PerformanceTemplateQuota::where($wheress)->get(['id','title','standard','weight','value'])->toArray();
                $value->list=$listsr;
            }
            if($lists){
                $info->list=$lists;
            }
            if($info->is_status==PerformanceApplication::API_STATUS_UNEVALUATED){
                $data['is_status']=PerformanceApplication::API_STATUS_IMPLEMENT;
                $data['updated_at']=date('Y-m-d H:i:s',time());
                PerformanceApplication::where($where)->update($data);
            }

            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }


    /*
     * 4-17    提交自评
     * gaolu
     */
    public function setReview($user_id,$arr){
        $where['id']=intval($arr['id']);
        $where['user_id']=$user_id;
        $where['status']=PerformanceApplication::API_STATUS_UNEVALUATED;
        $completion_value = $arr['content'];
        $evaluationCount=2;
        foreach ($completion_value as $completion){
            foreach ($completion as $compl){
                $num=count($compl);
                if($num!=$evaluationCount){
                    return returnJson($message='自评内容不能为空',$code=ConstFile::API_RESPONSE_FAIL);
                }
            }
        }

        $info=PerformanceApplication::where($where)->first();//->toArray();
        if($info){
            $wheres['pt_id']=$info->pt_id;
            $lists = PerformanceTemplateSon::where($wheres)->get(['id','approval_id']);
            foreach ($lists as $key=>$value){
                $data['auditor_id']=$value->approval_id;
                $data['pa_id']=intval($arr['id']);
                $data['pts_id']=$value->id;

                $ptsid=$value->id;
                $keyArr=array_keys($completion_value);
                if(in_array($ptsid, $keyArr)){
                    $valArr = $completion_value[$ptsid];
                }else{
                    return returnJson($message='访问参数错误',$code=ConstFile::API_RESPONSE_FAIL);
                }
                $wheress['pts_id']=$ptsid;
                $listsr = PerformanceTemplateQuota::where($wheress)->get(['id']);

                $ptqidStr='';
                $valueStr='';
                $rateStr='';
                foreach ($listsr as $keyss=>$value1){
                    if($keyss==0){
                        $ptqidStr=$value1->id;
                        $valueStr=$valArr[$value1->id][0];
                        $rateStr=$valArr[$value1->id][1];
                    }else{
                        $ptqidStr = $ptqidStr.','.$value1->id;
                        $valueStr = $valueStr.'|'.$valArr[$value1->id][0];
                        $rateStr = $rateStr.'|'.$valArr[$value1->id][1];
                    }

                }
                $data['ptq_id']=$ptqidStr;
                $data['completion_value']=$valueStr;
                $data['completion_rate']=$rateStr;
                $data['created_at']=date('Y-m-d H:i:s',time());
                $listsss[]=$data;
            }
            DB::transaction(function() use($listsss, $user_id,$arr) {
                $n = PerformanceApplicationSon::insert($listsss);
                if ($n) {
                    $where = array();
                    $where['id'] = intval($arr['id']);
                    $where['user_id'] = $user_id;
                    $datas['status'] = PerformanceApplication::API_STATUS_IMPLEMENT;
                    $datas['updated_at'] = date('Y-m-d H:i:s', time());
                    PerformanceApplication::where($where)->update($datas);
                }
            });
            return returnJson($message='自评成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }

    /*
     * 4-17    绩效模板删除
     * gaolu
     */
    public function setdel($uid,$id) {
        //$where['user_id']=$uid;
        $where['id']=intval($id);
        $where['status']=PerformanceTemplate::API_STATUS_SUCCESS;
        $data['status']=PerformanceTemplate::API_STATUS_DELETE;
        $data['created_at']=date('Y-m-d H:i:s', time());
        $n=PerformanceTemplate::where($where)->update($data);
        if($n){
            return returnJson($message='删除成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='删除失败',$code=ConstFile::API_RESPONSE_FAIL);
    }
    /*
     * 4-18    部门绩效--列表接口(打分人)
     * gaolu
     */
    public function getDpList($uid,$arr) {
        $limint=10;
        if(!empty($arr['limint']) && isset($arr['limint'])){
            $limint=intval($arr['limint']);
        }
        $where['a.auditor_id']=$uid;
        if(!empty($arr['amonth'])){
            $where['b.amonth']=trim($arr['amonth']);
        }
        //var_dump($where);die;
        $list=DB::table('performance_application_son as a')
            ->leftJoin('performance_application as b','a.pa_id','=','b.id')
            ->leftJoin('users as c','b.user_id','=','c.id')
            ->where($where)
            ->orderBy('a.status','asc')
            ->orderBy('a.id','desc')
            ->get(['a.id','a.pa_id','a.status as astatus','b.title','b.result','b.status','c.chinese_name','c.avatar','a.created_at','b.user_id'])
            ->paginate($limint);
        //var_dump($list);
        if($list){
            $list=$list->toArray();
        }else{
            $list=[];
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }
    /*
     * 4-18    绩效详情--打分界面
     * gaolu
     */
    public function getDpScoring($uid,$arr) {
        $where['a.id']=intval($arr['id']);
        $where['a.auditor_id']=$uid;

        $info = DB::table('performance_application_son as a')
                ->leftJoin('performance_application as b','a.pa_id','=','b.id')
                ->leftJoin('users as c','b.user_id','=','c.id')
                ->leftJoin('performance_template_son as d','a.pts_id','=','d.id')
                ->leftJoin('performance_template as e','b.pt_id','=','e.id')
                ->where($where)
                ->first(['a.id','a.pa_id','a.ptq_id','a.completion_value','a.completion_rate','a.status','a.created_at',
                    'b.number','c.chinese_name','c.avatar','d.title as dtitle','d.numb','e.number as enumber']);
        if($info){
            $ptq_id=explode(",",$info->ptq_id);
            $completion_value=explode('|',$info->completion_value);
            $completion_rate=explode('|',$info->completion_rate);
            ///$wheret[]=['id','in',$info->ptq_id];
            $li=DB::table('performance_template_quota')->whereIn('id',$ptq_id)
                ->get(['id','title','standard','weight','value'])->toArray();
            foreach ($li as $key=>&$val){
                $completion_valueStr=$completion_value[$key];
                $completion_rateStr =$completion_rate[$key];
                $val->completion_value=$completion_valueStr;
                $val->completion_rate=$completion_rateStr;
            }
            unset($info->completion_value);
            unset($info->completion_rate);
            unset($info->ptq_id);
            $info->list=$li;
            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
        }

        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }


    /*
     * 4-18    绩效审核打分
     * gaolu
     */
    public function setAuditScoring($uid,$arr) {
        $where['a.id']=intval($arr['id']);
        $where['a.auditor_id']=$uid;
        $where['a.status']=0;
        $val=$arr['content'];
        if(empty($arr['content'])){
            return returnJson($message='打分值不能为空',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['content'])){
            return returnJson($message='打分值不能为空',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['type'])){
            $type=1;//
        }else{
            $type=intval($arr['type']);
        }

        $info = DB::table('performance_application_son as a')
            ->leftJoin('performance_application as b','a.pa_id','=','b.id')
            ->where($where)
            ->first(['a.pa_id','a.ptq_id','b.audit_times','b.money','b.pt_id']);
        //var_dump($info);
        $where=array();
        if($info){
            $ptq_id=explode(",",$info->ptq_id);
            $sum = count($ptq_id);
            if(count($val)!==$sum){
                return returnJson($message='打分值没有填写完成',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $sums=array_sum($val);
            $score=implode("|",$val);
            $data['score']=$score;
            $data['total_score']=$sums;
            $data['updated_at']=date('Y-m-d H:i:s',time());
            if($type==PerformanceApplicationSon::API_STATUS_SUCCESS){
                $data['status']=PerformanceApplicationSon::API_STATUS_SUCCESS;
            }
            $where['id']=intval($arr['id']);
            $where['auditor_id']=$uid;
            try{
                DB::transaction(function() use($type, $where, $data,$info) {
                    //维度指标打分
                    $n=PerformanceApplicationSon::where($where)->update($data);
                    if($type!==PerformanceApplicationSon::API_STATUS_SUCCESS){
                        $n=false;
                    }
                    if($n){
                        $data=array();
                        $sum = $info->audit_times-1;
                        if($sum>0){
                            $data['audit_times']=$sum;
                        }else{
                            $where=array();
                            $where['pa_id']=$info->pa_id;
                            //计算总打分数
                            $sum= PerformanceApplicationSon::where($where)->sum('total_score');
                            $data['audit_times']=0;
                            $data['status']=PerformanceApplication::API_STATUS_PENDING;//审核成功 等待确认打分没有问题
                            $data['result']=$sum;//总分数
//                            $money=intval(($info->money*$sum)/100);
//                            $data['money']=$money;
                            //新版修改
                           $valuses = PerformanceTemplateContent::where('pt_id',$info->pt_id)->where('start','>=',$sum)->where('end','<=',$sum)->first('type','value');
                           $data['money']=$valuses->money;
                           if($valuses->type==PerformanceTemplateContent::API_TYPE_PUNISHMENT){
                               $data['money']=-$valuses->money;
                           }
                            //绩效可得金额
                        }
                        //维度都审核完成过后修改绩效申请记录表状态
                        $where=array();
                        $where['id']=$info->pa_id;
                        PerformanceApplication::where($where)->update($data);

                    }
                });
                if($type==PerformanceApplicationSon::API_STATUS_SUCCESS){
                    return returnJson($message='打分成功',$code=ConstFile::API_RESPONSE_SUCCESS);
                }else{
                    return returnJson($message='保存成功',$code=ConstFile::API_RESPONSE_SUCCESS);
                }
            }catch (Exception $e){
                if($type==PerformanceApplicationSon::API_STATUS_SUCCESS){
                    return returnJson($message='打分失败',$code=ConstFile::API_RESPONSE_FAIL);
                }else{
                    return returnJson($message='保存失败',$code=ConstFile::API_RESPONSE_FAIL);
                }
            }
        }
        if($type==PerformanceApplicationSon::API_STATUS_SUCCESS){
            return returnJson($message='打分失败',$code=ConstFile::API_RESPONSE_FAIL);
        }else{
            return returnJson($message='保存失败',$code=ConstFile::API_RESPONSE_FAIL);
        }
    }
    /*
    * 4-18    绩效打分完成界面--详情接口
    * gaolu
    */
    public function getDpScoringEnd($uid,$arr)
    {
        $where['a.id']=intval($arr['id']);
        $where['a.auditor_id']=$uid;
        $where['a.status']=PerformanceApplicationSon::API_STATUS_SUCCESS;
        $info = DB::table('performance_application_son as a')
            ->leftJoin('performance_application as b','a.pa_id','=','b.id')
            ->leftJoin('users as c','b.user_id','=','c.id')
            ->leftJoin('performance_template_son as d','a.pts_id','=','d.id')
            ->leftJoin('performance_template as e','b.pt_id','=','e.id')
            ->where($where)
            ->first(['a.id','a.pa_id','a.ptq_id','a.completion_value','a.completion_rate','a.score','a.status','a.created_at','a.total_score',
                'b.number','b.result','b.money','c.chinese_name','c.avatar','d.title as dtitle','d.numb','e.number as enumber']);
        if($info){
            $ptq_id=explode(",",$info->ptq_id);
            $completion_value=explode('|',$info->completion_value);
            $completion_rate=explode('|',$info->completion_rate);
            $score=explode('|',$info->score);
            ///$wheret[]=['id','in',$info->ptq_id];

            $li=PerformanceTemplateQuota::whereIn('id',$ptq_id)
                ->get(['id','title','standard','weight','value']);
            foreach ($li as $key=>&$val){
                $completion_valueStr=$completion_value[$key];
                $completion_rateStr =$completion_rate[$key];
                $scoreStr =$score[$key];
                $val->completion_value=$completion_valueStr;
                $val->completion_rate=$completion_rateStr;
                $val->score=$scoreStr;
            }
            unset($info->completion_value);
            unset($info->completion_rate);
            unset($info->score);
            unset($info->ptq_id);
            $info->list=$li;
            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
        }

        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);

    }

    /*
    * 4-19    绩效自评详情 (打分人)
    * gaolu
    */
    public function getDpevaluate($uid,$arr)
    {
        $where['a.id']=intval($arr['id']);
        $where['a.auditor_id']=$uid;
        $info = DB::table('performance_application_son as a')
            ->leftJoin('performance_application as b','a.pa_id','=','b.id')
            ->leftJoin('users as c','b.user_id','=','c.id')
            ->leftJoin('performance_template_son as d','a.pts_id','=','d.id')
            ->where($where)
            ->first(['a.id','a.pa_id','a.ptq_id','a.completion_value','a.completion_rate','a.status','a.created_at',
                'b.number','b.money','c.chinese_name','c.avatar','d.title as dtitle','d.numb']);
        if($info){
            $ptq_id=explode(",",$info->ptq_id);
            $completion_value=explode('|',$info->completion_value);
            $completion_rate=explode('|',$info->completion_rate);
            $li=PerformanceTemplateQuota::whereIn('id',$ptq_id)
                ->get(['title','standard','weight','value']);//->toArray();
            foreach ($li as $key=>&$val){
                $completion_valueStr=$completion_value[$key];
                $completion_rateStr =$completion_rate[$key];
                $val->completion_value=$completion_valueStr;
                $val->completion_rate=$completion_rateStr;
            }
            unset($info->completion_value);
            unset($info->completion_rate);
            unset($info->ptq_id);
            $info->list=$li;

            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
        }

        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);

    }

    /*
     * 4-19    绩效申请详情--执行中绩效,已完成（被审核人）
     * gaolu    $type  2表示完成待确定
     */
    public function getScoringInfo($user_id,$arr){
        //$where['view_password']=trim($arr['password']);
        $where['id']=intval($arr['id']);
        $where['user_id']=$user_id;
        $type=intval($arr['type']);
        if(empty($arr['type'])){
            $where['status']=PerformanceApplication::API_STATUS_IMPLEMENT;
        }elseif($type==PerformanceApplication::API_STATUS_PENDING){
            $where[]=['status','>=',PerformanceApplication::API_STATUS_PENDING];
        }else{
            $where['status']=PerformanceApplication::API_STATUS_IMPLEMENT;
        }

        $info=PerformanceApplication::where($where)
              ->first(['id','title','pt_id','result','result','status','number','amonth','money']);//->toArray();
        //var_dump($info);//die;
        if($info){
            $wheres['a.pa_id']=$info->id;
            $lists = DB::table('performance_application_son as a')
                ->leftJoin('performance_template_son as c','a.pts_id','=','c.id')
                ->leftJoin('users as b','a.auditor_id','=','b.id')
                ->where($wheres)
                ->get(['a.id','c.title','c.numb','a.total_score','a.score','a.ptq_id','a.auditor_id','a.status','b.avatar','b.chinese_name']);

            foreach ($lists as &$value){
                $ptqIdArr=explode(',',$value->ptq_id);
                $scoreArr=explode('|',$value->score);
                //var_dump($scoreArr);die;
                $listsr=PerformanceTemplateQuota::whereIn('id', $ptqIdArr)->get(['id','title','standard','weight','value']);
                foreach ($listsr as $keytt=>&$values){
                    $values->score= 0;
                    if($value->status==PerformanceApplication::API_STATUS_IMPLEMENT){
                        $values->score= $scoreArr[$keytt];
                    }
                }
                unset($value->score);
                $value->list=$listsr;
            }
            if($lists){
                $info->list=$lists;
            }

            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }

    /*
     * 4-19   被审核人接受打分（绩效完成）
     * gaolu
     */
    public function getAcceptScoring($user_id,$arr){
        $where['id']=intval($arr['id']);
        $where['user_id']=$user_id;
        $where['status']=PerformanceApplication::API_STATUS_PENDING;
        $data['status']=PerformanceApplication::API_STATUS_SUCCESS;
        $data['updated_at']=date('Y-m-d H:i:s');
        $n=PerformanceApplication::where($where)->update($data);
        if($n){
            return returnJson($message='确定成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);

    }

    /*
     * 4-22   绩效审核驳回操作
     * gaolu
     */
    public function setAuditReject($user_id,$arr){
        $where['id']=intval($arr['id']);
        $where['auditor_id']=$user_id;
        $where['status']=PerformanceApplicationSon::API_STATUS_DRAFT;
        $data['status']=PerformanceApplicationSon::API_STATUS_REJECT;
        $data['updated_at']=date('Y-m-d H:i:s');
        $paid= PerformanceApplicationSon::where($where)->value('pa_id');
        if($paid){
            PerformanceApplicationSon::where($where)->update($data);
            $where=array();
            $data=array();
            $where['id']=$paid;
            $where['status']=PerformanceApplication::API_STATUS_IMPLEMENT;
            $data['status']=PerformanceApplication::API_STATUS_REJECT;
            $data['updated_at']=date('Y-m-d H:i:s',time());
            PerformanceApplication::where($where)->update($data);

            return returnJson($message='驳回成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }

    /*
     * 4-22   绩效驳回重新自评接口--被审核人
     * gaolu
     */
    public function setRejectReview($user_id,$arr){
        $where['id']=intval($arr['id']);
        $where['user_id']=$user_id;
        $where['status']=PerformanceApplication::API_STATUS_REJECT;
        $completion_value = $arr['content'];
        foreach ($completion_value as $completion){
            foreach ($completion as $compl){
                $num=count($compl);
                if($num!=2){
                    return returnJson($message='自评内容不能为空',$code=ConstFile::API_RESPONSE_FAIL);
                }
            }
        }

        $info=PerformanceApplication::where($where)->first();//->toArray();
        if($info){
            $wheres['pt_id']=$info->pt_id;
            $keyArr=array_keys($completion_value);
            //var_dump($keyArr);//die;
            //$wheres[]=['id','in',$keyArr];
            $lists = PerformanceTemplateSon::where($wheres)->whereIn('id',$keyArr)->get(['id','approval_id']);
            //var_dump($lists);die;
            if(!$lists ){
                return returnJson($message='访问参数错误',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $where=array();
            foreach ($lists as $key=>$value){
                $where['pa_id']=intval($arr['id']);
                $where['pts_id']=$value->id;

                $ptsid=$value->id;
                $valArr = $completion_value[$ptsid];
                $wheress['pts_id']=$ptsid;
                $listsr = PerformanceTemplateQuota::where($wheress)->get(['id']);

                $valueStr='';
                $rateStr='';
                foreach ($listsr as $keyss=>$value1){
                    if($keyss==0){
                        $valueStr=$valArr[$value1->id][0];
                        $rateStr=$valArr[$value1->id][1];
                    }else{
                        $valueStr = $valueStr.'|'.$valArr[$value1->id][0];
                        $rateStr = $rateStr.'|'.$valArr[$value1->id][1];
                    }

                }
                $data['completion_value']=$valueStr;
                $data['completion_rate']=$rateStr;
                $data['created_at']=date('Y-m-d H:i:s',time());
                $data['status']=PerformanceApplicationSon::API_STATUS_DRAFT;
                $n =  PerformanceApplicationSon::where($where)->update($data);
            }

            if($n){
                $where=array();
                $where['id']=intval($arr['id']);
                $where['user_id']=$user_id;
                $datas['status']=PerformanceApplication::API_STATUS_IMPLEMENT;
                $datas['updated_at']=date('Y-m-d H:i:s',time());
                PerformanceApplication::where($where)->update($datas);
            }
            return returnJson($message='自评成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }
    /*
     * 4-22   绩效驳回重新自评详情页面接口
     * gaolu
     */
    public function getRejectInfo($user_id,$arr){
        //$where['view_password']=trim($arr['password']);
        $where['id']=intval($arr['id']);
        $where['user_id']=$user_id;
        $where['status']=PerformanceApplication::API_STATUS_REJECT;

        $info=PerformanceApplication::where($where)->first(['id','title']);//->toArray();
        //var_dump($info);//die;
        if($info){
            $wheres['a.pa_id']=$info->id;
            $wheres['a.status']=PerformanceApplicationSon::API_STATUS_REJECT;
            $lists = DB::table('performance_application_son as a')
                ->leftJoin('performance_template_son as c','a.pts_id','=','c.id')
                ->leftJoin('users as b','a.auditor_id','=','b.id')
                ->where($wheres)
                ->get(['a.pts_id as id','c.title','c.numb','a.ptq_id','a.auditor_id','a.status','b.avatar','b.chinese_name']);

            foreach ($lists as &$value){
                $ptqIdArr=explode(',',$value->ptq_id);
                $listsr=PerformanceTemplateQuota::whereIn('id', $ptqIdArr)->get(['id','title','standard','weight','value']);
                unset($value->ptq_id);
                $value->list=$listsr;
            }
            if($lists){
                $info->list=$lists;
            }

            return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
        }
        return returnJson($message='操作错误',$code=ConstFile::API_RESPONSE_FAIL);
    }

    /* PHP不使用速算扣除数计算个人所得税
     * @param float $salary 含税收入金额
     * @param float $deduction 保险等应当扣除的金额 默认值为0
     * @param float $threshold 起征金额 默认值为5000
     * @return float | false 返回值为应缴税金额 参数错误时返回false
     */
    public function getPersonalIncomeTax($salary, $deduction=0, $threshold=5000){
        if(!is_numeric($salary) || !is_numeric($deduction) || !is_numeric($threshold)){
            return false;
        }
        if($salary <= $threshold){
            return 0;
        }
        $levels = array(3000, 12000, 25000, 35000, 55000, 80000, PHP_INT_MAX);
        $rates = array(0.03, 0.1, 0.2, 0.25, 0.3, 0.35, 0.45);
        $taxableIncome = $salary - $threshold - $deduction;
        $tax = 0;
        foreach($levels as $k => $level){
            $previousLevel = isSet($levels[$k-1]) ? $levels[$k-1] : 0;
            if($taxableIncome <= $level){
                $tax += ($taxableIncome - $previousLevel) * $rates[$k];
                break;
            }
            $tax += ($level-$previousLevel) * $rates[$k];
        }
        $tax = round($tax, 2);
        //我心痛啊
        return $tax;
    }

    /**
     * 获取有绩效员工的绩效薪资
     */
    public function  getSummary($day='2019-05'){
        $user_id = Auth::id();
        $where['amonth']=trim($day);
        $listArr=PerformanceApplication::where($where)->whereIn('status',[0,1,2,7])->get(['id','user_id','title','status']);
        //dd($listArr);
        //发送消息
        if($listArr->toArray()){
            $datas=[];
            foreach($listArr as  $key=>$value){
                if($value->status==PerformanceApplication::API_STATUS_UNEVALUATED && $value->status==PerformanceApplication::API_STATUS_REJECT ){
                    $data = [
                        'receiver_id' => $value->user_id,//接收者（申请人）
                        'sender_id' => $user_id,//发送者（最后审批人）
                        'content'=> $value->title.'绩效自评未完成',//内容
                        'type' => Message::MESSAGE_TYPE_ACHIEVEMENTS,
                        'relation_id' => $value->id,//workflow_entries 的 id
                        'created_at'=>date('Y-m-d H:i:s',time()),
                        'updated_at'=>date('Y-m-d H:i:s',time())
                    ];
                }else{
                    $data = [
                        'receiver_id' => $value->user_id,//接收者（申请人）
                        'sender_id' => $user_id,//发送者（最后审批人）
                        'content'=> $value->title.'绩效没有确定',//内容
                        'type' => Message::MESSAGE_TYPE_ACHIEVEMENTS_ONE,
                        'relation_id' => $value->id,//workflow_entries 的 id
                        'created_at'=>date('Y-m-d H:i:s',time()),
                        'updated_at'=>date('Y-m-d H:i:s',time())
                    ];
                }

                $datas[]=$data;
            }
            Message::insert($datas);
            return returnJson($message='还有['.count($listArr->toArray()).']个员工的绩效没有完成',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $list=PerformanceApplication::where($where)->whereIn('status',[3,4,5,6])->pluck('money','user_id');
        if($list){
            return $list;
        }
        $list=[];
        return $list;
    }
}
