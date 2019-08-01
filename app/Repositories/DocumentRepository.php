<?php

namespace App\Repositories;

use App\Models\Comments\TotalComment;
use App\Models\Document\Document;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Services\AuthUserShadowService;
use DB;
use App\Models\WorkReport;
use App\Models\WorkReportReceiver;
use App\Models\WorkReportRule;
use App\Constant\ConstFile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

/**
 * Class ReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DocumentRepository extends ParentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */


    public function model()
    {
        return Document::class;
    }



    public function showDocumentForm(Request $request)
    {
        try {
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_ADMINISTRATIVE_DOCUMENTS);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                'flow_id' => $flow->id,
                'template' => $flow->template->toArray(),
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }

    public function createDocumentFlow(Request $request)
    {

//        var_dump($request);exit;

        return $this->updateFlow($request, 0);
    }



    public function workflowShow(Request $request)
{
    try {
        //基础信息

        //用户信息
        $user = User::where('id', '=', Auth::id())->select('chinese_name')->first();
        $userInfo = $user->toArray();

        $id = $request->get('id');

        $doc = Document::where('id','=',$id)->first();

//           var_dump($doc);exit;

        $entryId = $doc->entry_id;
        $docUserId = $doc->user_id;

        $authAuditor = new AuthUserShadowService();
        $entry = Entry::findUserEntry($authAuditor->id(), $entryId);



        $templateForm = $this->fetchEntryTemplate($entry)->template_form;


//            var_dump($entry);exit;

        $showData = $this->fetchShowData($entry->entry_data, $templateForm);

        throw_if(!isset(Entry::$_status[$entry['status']]),new Exception(sprintf('不存在的状态:%s',$entry['status'])));

        $comments = TotalComment::query()
            ->where('type', '=', TotalComment::TYPE_DOCUMENT)
            ->where('entry_id', '=', $entryId)
            ->with(['user'])
            ->get();

        $processes = $this->fetchEntryProcess($entry);

        $entry_name ='';
        foreach ($processes as $kk=>$val){

            if(empty($val['status'])){
                $entry_name = $val['auditor_name'];
                break;
            }
        }

//        var_dump($entry_name);exit;

        $status =  isset(Entry::$_status[$entry['status']]) ? Entry::$_status[$entry['status']] : '';

        $entry_status = $entry_name.$status;


//        var_dump($showData);exit;
        $this->data = [
            'login_name' => $userInfo['chinese_name'],
            'login_id' => Auth::id(),
            'doc_user_id' =>$docUserId,
            'doc_id' => $id,
            'entry_id' => $entryId,
            'entry_status' =>$entry_status,
            'procs_id' => $entry->procsFirstNode()->id,
            'comment_for_end' => $comments,   // 整个流程完成之后的评论
            'show_data' => $showData,
            'processes' =>$processes
        ];
    } catch (Exception $e) {
        $this->message = $e->getMessage();
        $this->code = $e->getCode();
    }
    return $this->returnApiJson();
}



    /**
     * 审批人视角
     */
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function workflowAuthorityShow(Request $request)
    {
//        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $process = Proc::findUserProcAllStatus($authAuditor->id(), $id);
//        var_dump($process);exit;
            $entry = Entry::findOrFail($process->entry_id);
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);



        //用户信息
        $user = User::where('id', '=', Auth::id())->select('chinese_name')->first();
        $userInfo = $user->toArray();

        $entryId = $process->entry_id;

        $doc = Document::where('entry_id','=',$entryId)->first();

//           var_dump($doc);exit;


        $docUserId = $doc->user_id;
        $docId = $doc->id;

        $showData = $this->fetchShowData($entry->entry_data, $templateForm);

        throw_if(!isset(Entry::$_status[$entry['status']]),new Exception(sprintf('不存在的状态:%s',$entry['status'])));

        $comments = TotalComment::query()
            ->where('type', '=', TotalComment::TYPE_DOCUMENT)
            ->where('entry_id', '=', $entryId)
            ->with(['user'])
            ->get();

        $entry_name ='';


//        var_dump($process['status']);exit;

       /* foreach ($process as $kk=>$val){

            var_dump($val);exit;


        }*/

        if(empty($process['status'])){
           $status = "等待我审批";
        }else{
            $status =  isset(Entry::$_status[$entry['status']]) ? Entry::$_status[$entry['status']] : '';
        }

//        var_dump($entry_name);exit;



//        var_dump($status);exit;



            $this->data = [
                'login_name' => $userInfo['chinese_name'],
                'login_id' => Auth::id(),
                'doc_user_id' =>$docUserId,
                'doc_id' => $docId,
                'proc_id' => $id,
                'entry_id' => $entryId,
                'entry_status' =>$status,
                'procs_id' => $entry->procsFirstNode()->id,
                'comment_for_end' => $comments,   // 整个流程完成之后的评论
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry),
                'proc' => $process->toArray()
            ];


        /*} catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }*/
        return $this->returnApiJson();
    }

    public function passWorkflow(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                (new Workflow())->passWithNotify($request->get('id'));
            });
            $authAuditor = new AuthUserShadowService();
            $process = Proc::findUserProcAllStatus($authAuditor->id(), $request->get('id'));

            $entryId = $process->entry_id;

            //获取流签批相关数据
            $procsInfo = Entry::where('id', $entryId)->with('procs')->get()->toArray();

            //获取流签批相关数据
//            $procsInfo =$entry->procs;


            $procsId =collect($procsInfo)->pluck("user_id")->toarray();

            $posStr = implode("&",$procsId);

            $posStr = "&".$posStr."&";

             Document::where('entry_id','=',$entryId)->update([
                'authorized_userId'=>$posStr,
                'updated_at'=>date("Y-m-d H:i:s"),
            ]);




        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function rejectWorkflow(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                (new Workflow())->reject($request->get('id'), $request->input('content', ''));
            });



            $authAuditor = new AuthUserShadowService();
            $process = Proc::findUserProcAllStatus($authAuditor->id(), $request->get('id'));

            $entryId = $process->entry_id;

            //获取流签批相关数据
            $procsInfo = Entry::where('id', $entryId)->with('procs')->get()->toArray();

            //获取流签批相关数据
//            $procsInfo =$entry->procs;


            $procsId =collect($procsInfo)->pluck("user_id")->toarray();

            $posStr = implode("&",$procsId);

            $posStr = "&".$posStr."&";

            Document::where('entry_id','=',$entryId)->update([
                'status'=>1,
                'authorized_userId'=>$posStr,
                'updated_at'=>date("Y-m-d H:i:s"),
            ]);



        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }





    public function updateFlow(Request $request, $id)
    {
//        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');


            if(!$flow_id){
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            }

//            var_dump($flow_id);exit;get_type

            //$id = $request->get('id',0);
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            $entry = $this->updateOrCreateEntry($request, $id); // 创建或更新申请单

//            var_dump($entry->code);exit;

//            if(isset($entry->code)&&($entry->code==-1)){
//
//                return $entry;
//            }
//            var_dump($entry);exit;

            $entryId =$entry->id;


            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            DB::commit();
//            var_dump($procsId);exit;

            //获取前端传的数据
             $docData = $request->get('tpl');
            //获取流签批相关数据
            $procsInfo =$entry->procs;


            $procsId =collect($procsInfo)->pluck("user_id")->toarray();

            if($procsId){
                $posStr = implode("&",$procsId);

                $posStr = "&".$posStr."&";
            }else{
                $posStr ='';
            }


//            var_dump($procsStr);exit;


//             var_dump($docData['subject']);exit;

            $userId = Auth::id();

            $doc = [];

            DB::transaction(function () use ($docData,$entryId,$userId,$posStr,&$doc) {

//                var_dump($posStr);exit;

                //创建公文
                $docData['user_id'] = $userId;
                $docData['entry_id'] = $entryId;
                $docData['authorized_userId'] = $posStr;
                $docData['created_at'] = date('Y-m-d H:i:s');
                $doc = Document::create($docData);

//                return $doc;
            });

            $this->data = [
                'entry' => $entry->toArray(),
                'doc_id' => $doc->id
            ];
//            $this->data = ['doc_id' => $doc->id];
//        } catch (Exception $e) {
//            DB::rollback();
//            $this->message = $e->getMessage();
//            $this->code = $e->getCode();
//            Workflow::errLog('EntryUpdate', $e->getMessage() . $e->getTraceAsString());
//        }
        return $this->returnApiJson();
    }


    public function history($info)
    {
        // 获取有审批权限的
        $authAuditor = new AuthUserShadowService();
        $uid = $authAuditor->id();

        $loginId = Auth::id();

        $doclist = DB::table('document')->where(function ($query) use ($loginId){

            $query->where('user_id','=',$loginId)->orwhereIn('id',function($query1) use ($loginId){
                $query1->select('id')
                    ->from('document')
                    ->where('authorized_userId','like',"%&".$loginId."&%");
            });
        });


//        var_dump($info);exit;

        //中文名查询
        if(isset($info['chinese_name'])){

            $keywords = $info['chinese_name']."%";
            $userInfo = DB::table('users')->where('chinese_name','like',$keywords)->select('id')->get()->toArray();

            $userIds = array_pluck($userInfo,'id');

            $docId = [];
            $idArr = [];
            $docItems = [];

            if($userIds){
                foreach ($userIds as $kk=>$vv){
                    $docItems = DB::table('document')
                        ->where('authorized_userId','like',"%&".$vv."&%")
                        ->select('id')
                        ->get()
                        ->toArray();

                    if($docItems){
                        array_push($docId,array_column($docItems,'id'));
                    }
                }
            }

//            var_dump($docId);exit;

            foreach ($docId as $dovk => $docv){
                foreach ($docv as $dk=>$dv){
                    array_push($idArr,$dv);
                }
            }

            $ids = array_unique($idArr);

            $doclist =$doclist ->whereIn('id',$ids);
//            var_dump(array_unique($idArr));exit;

        }



        if(isset($info['time_search'])){
            //查看选择时间当天与之后的
            $doclist =$doclist ->where('created_at','>=', date("Y-m-d 00:00:00",strtotime($info['time_search'])));

            //查看当天的
//            $doclist =$doclist ->whereDate('created_at', date("Y-m-d",strtotime($info['time_search'])));
        }


        if(isset($info['primary_dept'])){
            $doclist =$doclist ->where('primary_dept','=',$info['primary_dept']);
//            $doclist =$doclist ->where('created_at','>=','2019-04-29 00:00:00');
        }

//        var_dump($info['primary_dept']);exit;

        $passDocList = $doclist;


        $doclist =$doclist ->select('id','entry_id','doc_title','user_id','created_at','status')->orderBy('id','desc')->get();


//        $passList = $passDocList
//            ->where('status',1)
//            ->select('id','entry_id','doc_title','user_id','created_at','status')->orderBy('id','desc')->get();

//        var_dump($doclist);exit;


        $passList =  []; //我发起的公文---审批通过的
        $applyList =  []; //我发起的公文---正在审批的
        $processList = []; //我审批的公文
        foreach ($doclist as $k=>$v){
            if($v->user_id == $loginId){

                if($v->status==1){
                    //审批通过的
                    $passList[] = $v;
                }elseif ($v->status==0){
                    //审批中的
                    $applyList[] = $v;
                }else{
                    //撤销的 不显示
                    continue;

                }
//                $doclist[$k]->belong_type = "self";

            }else{
//                $doclist[$k]->belong_type = "other";
                $procItem = [];
                $entryProcess = Entry::where('id', $v->entry_id)->with('procs')->get()->toArray();
                if(isset($entryProcess[0]['procs'])){
                    if(is_array($entryProcess[0]['procs'])){
                        foreach ($entryProcess[0]['procs'] as $k=>$proc){
                            if($proc['user_id'] == $loginId){
//                                var_dump($v->doc_title);exit;
                                //该公文数据属于我是审批身份的数据
                                $procItem['doc_title'] = $v->doc_title;
                                $procItem['doc_id'] = $v->id;
                                $procItem['proc_id'] = $proc['id'];
                                $processList[] = $procItem;
                            }
                        }
                    }else{
                        unset($doclist[$k]);
                    }
                }else{
                    unset($doclist[$k]);
                }

            }
        }

//        var_dump($processList);exit;
        $this->data = ['passlist' => $passList, 'applylist' => $applyList, 'processlist' => $processList];

        return $this->returnApiJson();
    }



    public function updateOrCreateEntry(Request $request, $id = 0)
    {
        $data = $request->all();
        $data['file_source_type'] = 'workflow';
        $data['file_source'] = 'administrative_documents';
        $data['is_draft'] = null;
        $data['entry_id'] = null;

        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            $entry = Entry::create([
                'title' => $data['title'],
                'flow_id' => $data['flow_id'],
                'user_id' => $authApplyer->id(),
                'circle' => 1,
                'status' => Entry::STATUS_IN_HAND,
                'origin_auth_id' => Auth::id(),
                'origin_auth_name' => Auth::user()->name,
            ]);

        } else {
            $entry = Entry::findOrFail($id);
            $entry->checkEntryCanUpdate(); // 校验申请单是否可以修改
            $entry->update($data);
        }


//        var_dump($entry);exit;

        if (!empty($data['is_draft'])) {
            $entry->status = Entry::STATUS_DRAFT;
        } else {
            $entry->status = Entry::STATUS_IN_HAND;
        }

/*
        if(!$data['tpl']){
//            var_dump(ConstFile::API_RESPONSE_FAIL);exit;
            return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            exit;
        }
        foreach ($data['tpl'] as $k => $v) {
//            var_dump($v);exit;
            $val = is_array($v) ? json_encode($v) : $v;
            $val = $val === null ? '' : $val;

            if(empty($val)){
//                var_dump($val);exit;
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
                break;
            }

            var_dump(111);
        }


        var_dump($data['tpl']);exit;*/
        $this->updateTpl($entry, $data['tpl'] ?? []);

        return $entry;
    }

    public function updateTpl(Entry $entry, $tpl = [])
    {

//        var_dump($tpl);exit;


        foreach ($tpl as $k => $v) {
            $val = is_array($v) ? json_encode($v) : $v;
            $val = $val === null ? '' : $val;
            EntryData::updateOrCreate(['entry_id' => $entry->id, 'field_name' => $k], [
                'flow_id' => $entry->flow_id,
                'field_value' => $val,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }
    }

    private function fetchEntryTemplate(Entry $entry)
    {
        return $entry->pid > 0 ? $entry->parent_entry->flow->template : $entry->flow->template;
    }

    private function fetchEntryProcess(Entry $entry)
    {
        $processes = (new Workflow())->getProcs($entry);

//var_dump($processes);exit;
        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $processAuditors = $temp = [];
        foreach ($processes as $process) {
            $temp['process_name'] = $process->process_name;
            $temp['auditor_id'] = '';
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $process->proc ? $process->proc->content : '';

            if ($process->proc && $process->proc->auditor_name) {
                $temp['auditor_name'] = $process->proc->auditor_name;
            } elseif ($process->proc && $process->proc->user_name) {
                $temp['auditor_name'] = $process->proc->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            if ($process->proc && $process->proc->auditor_id) {
                $temp['auditor_id'] = $process->proc->auditor_id;
            } elseif ($process->proc && $process->proc->user_id) {
                $temp['auditor_id'] = $process->proc->user_id;
            } else {
                $temp['auditor_id'] = implode(",",$process->auditor_ids);
            }



            $temp['status'] = $process->proc ? $process->proc->status : '';
            $temp['status_name'] = '';

            if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                $temp['status_name'] = '驳回';
            } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                $temp['status_name'] = '完成';
            } else {
                $temp['status_name'] = '待处理';
            }
            $processAuditors[] = $temp;
        }

        return $processAuditors;
    }


    private function fetchShowData($entry, $templateForm)
    {
        $entry = collect($entry->toArray());

        $templateForm = collect($templateForm);


        if ($entry->isEmpty() || $templateForm->isEmpty()) {
            return [];
        }

        $result = [];
        $templateForm->each(function ($item, $key) use ($entry, &$result) {
            $temp = [];
            $collect = $entry->where('field_name', $item->field);
            if (!in_array($item->field, ['div', 'email', 'password']) && $collect->isNotEmpty()) {
                $temp['title'] = $item['field_name'];
                $temp['value'] = ($collect->first())['field_value'];
                $result[] = $temp;
            }
        });
        return $result;
    }


    public function dealInfo($entry, $templateForm)
    {
        $info = [];
        /** @var Entry $entry */
        $entriesData = $entry->entry_data;

        foreach ($entriesData as $datum) {

            foreach ($templateForm as $form) {
                /** @var EntryData $datum */
                if ($datum->field_name == $form->field) {
                    $info[$form->field_name] = $datum->field_value;
                } else {
                    continue;
                }
            }
        }
        unset($entry->entry_data);
        return $info;
    }



}
