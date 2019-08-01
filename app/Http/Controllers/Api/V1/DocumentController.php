<?php

namespace App\Http\Controllers\Api\V1;

use App\Constant\ConstFile;
use App\Models\Document\Document;
use App\Models\OperateLog;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Repositories\DocumentRepository;
use App\Repositories\EntryRepository;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DocumentController extends BaseController
{

    /**
     * @var DocumentRepository
     */
    protected $document;

    public function __construct(DocumentRepository $document){
        parent::__construct();
        $this->document = $document;
        $this->repository = app()->make(EntryRepository::class);
    }

    //

    public function showDocumentForm(Request $request)
    {
        return $this->document->showDocumentForm($request);
    }

    public function createDocument(Request $request)
    {

        /*$arr = [
            'doc_title'=>'公文标题111',
            'brand'=>'公文字号111',
            'primary_dept'=>'370',
            'type'=>'1',
            'secret_level'=>'1',
            'emergency_level'=>'1',
            'theme_word'=>'主题词111',
            'authorized_person'=>'370',
            'CC_person'=>'370',
            'content'=>'文件内容111',
            'file_upload'=>'',
        ];*/


        /*
          'flow_id'=>51,
            'title'=>'行政文件',
           tpl[doc_title]=>'公文标题111',
            tpl[document_number]=>'公文字号111',
           tpl[primary_dept]=>'鹤瀚网络/技术部/智能ERP',
           tpl[doc_type]=>'批复',
           tpl[secret_level]=>'机密',
           tpl[urgency]=>'特级',
            tpl[subject]=>'主题词111',
            tpl[main_dept]=>'鹤瀚网络/技术部/智能ERP',
            tpl[copy_dept]=>'鹤瀚网络/技术部/智能ERP',flow_show
            tpl[content]=>'文件内容111',
            tpl[file_upload]=>'',
        */


        return $this->document->createDocumentFlow($request);
    }



    public function showWorkflow(Request $request)
    {
//        $id = $request->get('id');
//        var_dump($id);exit;
        return $this->document->workflowShow($request);
    }
    public function showAuditorWorkflow(Request $request)
    {
//        $id = $request->get('id');
//        var_dump($id);exit;
        return $this->document->workflowAuthorityShow($request);
    }



    /**
     * 撤销
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function cancelWorkflow(Request $request){
        $uid = \Illuminate\Support\Facades\Auth::id();


//        var_dump($uid);exit;

        $entry_id = $request->get('entry_id');
        $content = $request->get('content');
        $entryObj = Entry::find($entry_id);

        if(empty($content)){
            $content = Carbon::now()->toDateString() . '用户:' . Auth::user()->chinese_name . '撤销了ID为' . $entry_id . '的流程申请';
        }
        $data = [
            'operate_user_id' => $uid,
            'action' => 'cancel',
            'type' => OperateLog::TYPE_WORKFLOW,
            'object_id' => $entry_id,
            'object_name' => $entryObj->title,
            'content' => $content,
        ];
        OperateLog::query()->insert($data);
        $entryObj->status = Entry::STATUS_CANCEL;

        Document::where('entry_id','=',$entry_id)->update([
            'status'=>2,   //2表示公文被撤销
            'authorized_userId'=>'',
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);

        $res = $entryObj->save();
        //

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,
            ConstFile::API_RESPONSE_SUCCESS, $res);
    }

    public function passWorkflow(Request $request)
    {
        return $this->document->passWorkflow($request);
    }

    public function rejectWorkflow(Request $request)
    {
        return $this->document->rejectWorkflow($request);
    }


    public function showDocList(Request $request)
    {
        $info = $request->all();
//        var_dump($request);exit;
        return $this->document->history($info);
    }
}
