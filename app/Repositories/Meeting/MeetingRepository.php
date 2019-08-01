<?php

namespace App\Repositories\Meeting;

use App\Models\Basic\BasicOaOption;
use App\Models\Basic\BasicOaType;
use App\Models\Meeting\Meeting;
use App\Models\Meeting\MeetingParticipant;
use App\Models\Meeting\MeetingRoom;
use App\Models\Meeting\MeetingRoomConfig;
use App\Models\Meeting\MeetingTask;
use App\Models\Message\Message;
use App\Models\OperateLog;
use App\Models\Seals\Seals;
use App\Models\Seals\SealsType;
use App\Services\Workflow\FlowCustomize;
use App\Services\AuthUserShadowService;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Constant\ConstFile;
use App\Models\User;
use Exception;
use Auth;
use DB;


/**
 * Class UsersRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MeetingRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MeetingRoom::class;
    }

    private $field = [
        'title' => '请填写会议室名称',
        'position' => '请填写会议室位置',
        'administrators_id' => '请选择管理员',
        'department_id' => '请选择管理员部门',
        'configure' => '请选择设备配置',
        'number' => '请填写可容纳人数',
        'status' => '请选择启用状态',
        'start' => '请选择预约开始时间',
        'end' => '请选择预约结束时间',
        'small_time_lapse' => '请选择可预约最小时间段',
        //'large_time_lapse' => '请选择可预约最大时间段'
        'predictable_scope' => '请选择可预约范围'
    ];
    /*
     * 检测会议室提交的数据
     * */
    private function checkGoodsData($param){
        $msg = '';

        foreach($param['need'] as $k => $v){
            if(in_array($k, array_keys($this->field)) && empty($v)){
                $msg = $this->field[$k];
            }
        }
        return $msg;
    }
    /*
     * 7-3 修改
     * gaolu
     * 添加会议室
     */
    public function setAddOne($uid,$arr) {
        $msg = $this->checkGoodsData($arr);
        if($msg){
            return returnJson($message=$msg,$code=ConstFile::API_RESPONSE_FAIL);
        }

        $id=0;
        if(!empty($arr['id']) && isset($arr['id'])){//备注
            $id=intval($arr['id']);
        }
        if(!empty($arr['remarks']) && isset($arr['remarks'])){//备注
            $data['remarks']=trim($arr['remarks'])?trim($arr['remarks']):'';
        }
        if($arr['start']>$arr['end']){
            return returnJson($message='开始时间不能大于最后预约时间!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['user_id']=$uid;
        $data['position']=trim($arr['position']);
        $data['number']=trim($arr['number']);
        $data['start']=trim($arr['start']);
        $data['end']=trim($arr['end']);

        $data['administrators_id']=intval($arr['administrators_id']);
        $data['department_id']=intval($arr['department_id']);
        $data['small_time_lapse']=intval($arr['small_time_lapse']);
        $data['large_time_lapse']=intval($arr['large_time_lapse']);
        $data['predictable_scope']=intval($arr['predictable_scope']);
        $data['enabled_state']=intval($arr['status']);
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        DB::beginTransaction();
        try{
            if($id){
                $dataOne['title']='修改'.$arr['title'];
            }else{
                $dataOne['title']=$arr['title'];
            }
            $entry = FlowCustomize::EntryFlow($dataOne, 'meeting_room');//添加会议室工作流
            $data['entrise_id'] = $entry->id;
            if($id){
                $ids= MeetingRoom::updated($data);
            }else{
                $ids= MeetingRoom::insertGetId($data);
            }

            $n=0;
            if($id) {
                foreach ($arr['configure'] as $key => $values) {
                    $datas['config_id'] = $values;
                    $datas['updated_at'] = date('Y-m-d H:i:s', time());
                    $datas['status'] = 1;
                    if($id){//修改
                        $wheres['id']=$values['id'];
                        MeetingRoomConfig::where($wheres)->update($datas);
                    }else{//添加
                        $datas['created_at'] = date('Y-m-d H:i:s', time());
                        $datas['mr_id'] = $ids;
                        $datat[] = $datas;
                    }
                }
                if(!$id){
                    $n = MeetingRoomConfig::insert($datat);
                }

            }
            if($n){
                DB::commit();
                return returnJson($message='操作成功',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$id);
            }else{
                DB::rollBack();
                return returnJson($message='操作失败',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            DB::rollBack();
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*
     * 7-3 修改
     * gaolu
     * 修改会议室
     */
    public function setUpdateMeeting($uid,$arr) {
        $msg = $this->checkGoodsData($arr);
        if($msg){
            return returnJson($message=$msg,$code=ConstFile::API_RESPONSE_FAIL);
        }

        if(!empty($arr['remarks']) && isset($arr['remarks'])){//备注
            $data['remarks']=trim($arr['remarks'])?trim($arr['remarks']):'';
        }
        if($arr['start']>$arr['end']){
            return returnJson($message='开始时间不能大于最后预约时间!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['user_id']=$uid;
        $data['code']=getCode('HYS');
        $data['title']=trim($arr['title']);
        $data['position']=trim($arr['position']);
        $data['number']=trim($arr['number']);
        $data['start']=trim($arr['start']);
        $data['end']=trim($arr['end']);

        $data['administrators_id']=intval($arr['administrators_id']);
        $data['department_id']=intval($arr['department_id']);
        $data['small_time_lapse']=intval($arr['small_time_lapse']);
        $data['large_time_lapse']=intval($arr['large_time_lapse']);
        $data['predictable_scope']=intval($arr['predictable_scope']);
        $data['enabled_state']=intval($arr['status']);
        $data['created_at']=date('Y-m-d H:i:s',time());
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        DB::beginTransaction();
        try{
            $dataOne['title']=$arr['title'];
            $entry = FlowCustomize::EntryFlow($dataOne, 'meeting_room');//添加会议室工作流
            $data['entrise_id'] = $entry->id;
            $id= MeetingRoom::insertGetId($data);
            $n=0;
            if($id) {
                foreach ($arr['configure'] as $key => $values) {
                    $datas['mr_id'] = $id;
                    $datas['config_id'] = $values;
                    $datas['created_at'] = date('Y-m-d H:i:s', time());
                    $datas['updated_at'] = date('Y-m-d H:i:s', time());
                    $datas['status'] = 1;
                    $datat[] = $datas;
                }
                $n = MeetingRoomConfig::insert($datat);
            }
            if($n){
                DB::commit();
                return returnJson($message='添加成功',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$id);
            }else{
                DB::rollBack();
                return returnJson($message='添加失败',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            DB::rollBack();
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*
     * 4-25修改
     * gaolu
     * 添加会议室
     */
    public function setAdd($uid,$arr) {
        if(empty($arr['title'])){
            return returnJson($message='会议室名称不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['position'])){
            return returnJson($message='会议室位置不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['configure'])){
            return returnJson($message='设备配置不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['status'])){
            return returnJson($message='启用状态不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if($arr['number']!=0){
            if(empty($arr['number'])){
                return returnJson($message='可容纳人数不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }

        if(empty($arr['start'])){
            return returnJson($message='开始预约时间不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['end'])){
            return returnJson($message='最后预约时间不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!empty($arr['remarks']) && isset($arr['remarks'])){
            $data['remarks']=trim($arr['remarks'])?trim($arr['remarks']):'';
        }
        if($arr['start']>$arr['end']){
            return returnJson($message='开始时间不能大于最后预约时间!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['user_id']=$uid;
        $data['code']=getCode();
        $data['title']=trim($arr['title']);
        $data['position']=trim($arr['position']);
        $data['number']=trim($arr['number']);
        $data['status']=intval($arr['status']);
        $data['start']=trim($arr['start']);
        $data['end']=trim($arr['end']);
        $data['created_at']=date('Y-m-d H:i:s',time());
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        DB::beginTransaction();
        try{
            $dataOne['title']=$arr['title'];
            //$entry = FlowCustomize::EntryFlow($dataOne, 'meeting_room');//添加会议室工作流
            //$data['entrise_id'] = $entry->id;
            $id= MeetingRoom::insertGetId($data);
            $n=0;
            if($id) {
                foreach ($arr['configure'] as $key => $values) {
                    $datas['mr_id'] = $id;
                    $datas['config_id'] = $values;
                    $datas['created_at'] = date('Y-m-d H:i:s', time());
                    $datas['updated_at'] = date('Y-m-d H:i:s', time());
                    $datas['status'] = 1;
                    $datat[] = $datas;
                }
                $n = MeetingRoomConfig::insert($datat);
            }
            if($n){
                DB::commit();
                return returnJson($message='添加成功',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$id);
            }else{
                DB::rollBack();
                return returnJson($message='添加失败',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            DB::rollBack();
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*
     * 5-30 修改
     * gaolu
     * 会议室详情
     */
    public function getMeetingToomInfoOne($uid,$arr){
        if(!empty($arr['id']) && isset($arr['id'])){
            $where['id']=intval($arr['id']);
            $info=MeetingRoom::with('config.configName','userInfo')
                ->where($where)->first(['id','title','code','user_id','position','number','start','end','remarks','created_at','entrise_id']);
            if($info){
                $info->type=1;
                $info->pid=intval($arr['id']);
            }
        }
        if(!empty($arr['entrise_id']) && isset($arr['entrise_id'])){
            $where['entrise_id']=intval($arr['entrise_id']);
            $info=MeetingRoom::with('config.configName','userInfo')
                ->where($where)->first(['id','title','code','user_id','position','number','start','end','remarks','created_at','entrise_id']);
            if($info){
                $info->type=2;
                $info->pid=intval($arr['entrise_id']);
            }
        }
        if(!empty($arr['procs_id']) && isset($arr['procs_id'])){
            $where['id']=intval($arr['procs_id']);
            $where['user_id']=$uid;
            $entry_id = Proc::where($where)->value('entry_id');
            if(!$entry_id){
                return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
            }
            $where['entrise_id']=$entry_id;
            $info=MeetingRoom::with('config.configName','userInfo')
                ->where($where)->first(['id','title','code','user_id','position','number','start','end','remarks','created_at','entrise_id']);
            if($info){
                $info->type=1;
                $info->pid=intval($arr['procs_id']);
            }
        }
        if($info){
            $user=User::where('id',$info->user_id)->first();
            $dept = $user->getPrimaryDepartment;
            $infos = $dept->department->toArray();
            $info->department=$infos['name'];
            $info=$info->toArray();
            $config_name='';
            foreach ($info['config'] as $k=>$val){
                //var_dump($val->toArray());die;
                if($k==0){
                    $config_name=$val['config_name']['title'];
                }else{
                    $config_name.=','.$val['config_name']['title'];
                }

            }

            $info['config_name']=$config_name;
            unset($info['config']);
            $month = date('Y-m');
            $day = date('Y-m-d');
            $days = date('Y-m-d').'00:00:00';
            $info['list']= [];
            $info['process']=[];
            $info['daylist']= [];
            if($info['status']==2){
                //后期需要该 where('status',2)
                $daylist = Meeting::where('day','like',$month."%")->where('start','>',$days)->where('mr_id',$info['id'])->where('status',1)->groupBy('day')->get(['day']);
                if($daylist){
                    $info['daylist']= $daylist->toArray();
                }
                $infolist=Meeting::where('day',$day)->where('mr_id',$info['id'])->where('status',1)->get(['id','start','end']);
                if($infolist){
                    $info['list']= $infolist->toArray();
                }
                if($info['entrise_id']){
                    $entry = Entry::findOrFail($info['entrise_id']);
                    $info['process'] = $this->fetchEntryProcess($entry);
                }
            }
        }else{
            $info=[];
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
    }

    /*
     * 4-28
     * gaolu
     * 会议室详情
     */
    public function getMeetingToomInfo($uid,$arr){
        if(!empty($arr['id']) && isset($arr['id'])){
            $where['id']=intval($arr['id']);
            $info=MeetingRoom::with('config.configName','userInfo')
                ->where($where)->first(['id','title','code','user_id','position','number','start','end','remarks','created_at','entrise_id']);
            if($info){
                $info->type=1;
                $info->pid=intval($arr['id']);
            }
        }
        if(!empty($arr['entrise_id']) && isset($arr['entrise_id'])){
            $where['entrise_id']=intval($arr['entrise_id']);
            $info=MeetingRoom::with('config.configName','userInfo')
                ->where($where)->first(['id','title','code','user_id','position','number','start','end','remarks','created_at','entrise_id']);
            if($info){
                $info->type=2;
                $info->pid=intval($arr['entrise_id']);
            }
        }
        if(!empty($arr['procs_id']) && isset($arr['procs_id'])){
            $where['id']=intval($arr['procs_id']);
            $where['user_id']=$uid;
            $entry_id = Proc::where($where)->value('entry_id');
            if(!$entry_id){
                return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
            }
            $where['entrise_id']=$entry_id;
            $info=MeetingRoom::with('config.configName','userInfo')
                ->where($where)->first(['id','title','code','user_id','position','number','start','end','remarks','created_at','entrise_id']);
            if($info){
                $info->type=1;
                $info->pid=intval($arr['procs_id']);
            }
        }
         if($info){
             $user=User::where('id',$info->user_id)->first();
             $dept = $user->getPrimaryDepartment;
             $infos = $dept->department->toArray();
             $info->department=$infos['name'];
             $info=$info->toArray();
             $config_name='';
             foreach ($info['config'] as $k=>$val){
                 //var_dump($val->toArray());die;
                 if($k==0){
                     $config_name=$val['config_name']['title'];
                 }else{
                     $config_name.=','.$val['config_name']['title'];
                 }

             }

             $info['config_name']=$config_name;
             unset($info['config']);
             $month = date('Y-m');
             $day = date('Y-m-d');
             $days = date('Y-m-d').'00:00:00';
             //后期需要该 where('status',2)
             $daylist = Meeting::where('day','like',$month."%")->where('start','>',$days)->where('mr_id',$info['id'])->where('status',2)->groupBy('day')->get(['day']);
             $info['daylist']= [];
             if($daylist){
                 $info['daylist']= $daylist->toArray();
             }

             $infolist=Meeting::where('day',$day)->where('mr_id',$info['id'])->where('status',2)->get(['id','start','end']);
             $info['list']= [];
             if($infolist){
                 $info['list']= $infolist->toArray();
             }
             $info['process']=[];
             if($info['entrise_id']){
                 $entry = Entry::findOrFail($info['entrise_id']);
                 $info['process'] = $this->fetchEntryProcess($entry);
             }

        }else{
             $info=[];
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
    }
    /*
     * 4-28
     * gaolu
     * 会议室某个月预约会议的时间（2019-04-28）
     */
    public function monthList($uid,$arr){
        $month=trim($arr['month']);
        $id=intval($arr['id']);
        //后期需要该 where('status',2)
        $info =Meeting::where('day','like',$month."%")->where('mr_id',$id)->where('status',2)->groupBy('day')->get(['day'])->toArray();
        if(!$info){
            $info=[];
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$info);
    }

    /*
     * 4-28修改
     * gaolu
     * 会议室某一天所有预约的会议
     */
    public function getDayList($uid,$arr){
        $day = $arr['day'];
        $id=intval($arr['id']);
        //后期需要该 where('status',2)
        $list = Meeting::where('day',$day)->where('mr_id',$id)->where('status',2)->get(['id','start','end'])->toArray();
        if(!$list){
            $list = [];
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$list);
    }

    /*
     * 4-24修改
     * gaolu
     * 会议室列表搜索
     */
    public function getList($uid,$arr) {
        if(!empty($arr['title']) && isset($arr['title'])){
            $where[]=['title','like','%'.trim($arr['title']).'%'];
        }
        if(!empty($arr['status']) && isset($arr['status'])){
            $where['status']=intval($arr['status']);
        }else{
            $where[]=['status','>',0];
        }
        if(!empty($arr['start']) && isset($arr['start'])){
            $where[]=['start','<=',$arr['start'] ];
            $where[]=['end','>=',$arr['start'] ];
        }
        if(!empty($arr['configure']) && isset($arr['configure'])){
            $configure=$arr['configure'];
            //dd($configure);
            $list = MeetingRoomConfig::query()->whereIn('config_id',$configure)
                ->with(['meetingRoom'=> function ($query) use ($where){
                    $query->where($where)->get(['id','title','code','position','number','start','end','remarks','status','created_at']);
                },'meetingRoom.config.configName'])
                ->selectRaw('mr_id, count(id) as count_id')
                ->groupBy('mr_id')
                ->orderBy('mr_id','desc')
                ->havingRaw('count(id)>='.count($configure))
                ->paginate(10);
            if($list){
                $list=$list->toArray();
                $listArr=[];
                foreach ($list['data'] as $k=>$v){
                    if(!$v['meeting_room']){
                        continue;

                    }
                    $config = $v['meeting_room']['config'];
                    $title="";
                    foreach ($config as $ks=>$vl){
                        if($ks==0){
                            $title = $vl['config_name']['title'];
                        }else{
                            $title =$title.','. $vl['config_name']['title'];
                        }

                    }
                    $v['meeting_room']['config_name']=$title;
                    unset($v['meeting_room']['config']);
                    $listArr[] =  $v['meeting_room'];
                }
                $list['data']=$listArr;
                unset($list['first_page_url']);
                unset($list['from']);
                unset($list['last_page_url']);
                unset($list['next_page_url']);
                unset($list['path']);
                unset($list['prev_page_url']);
            }
        }else{
            $list = MeetingRoom::with('config.configName')->where($where)->orderBy('id','desc')
                    ->select(['id','title','code','position','number','start','end','remarks','status','created_at'])->paginate(10);
            if($list){
                $list=$list->toArray();
                $listArr=[];
                foreach ($list['data'] as $k=>$v){
                    $config = $v['config'];
                    $title="";
                    foreach ($config as $ks=>$vl){
                        if($ks==0){
                            $title = $vl['config_name']['title'];
                        }else{
                            $title =$title.','. $vl['config_name']['title'];
                        }

                    }
                    $v['config_name']=$title;
                    unset($v['config']);
                    $listArr[] =  $v;
                }
                $list['data']=$listArr;
                unset($list['first_page_url']);
                unset($list['from']);
                unset($list['last_page_url']);
                unset($list['next_page_url']);
                unset($list['path']);
                unset($list['prev_page_url']);

            }
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$list);
    }

    /*
     * 4-28修改
     * gaolu
     * 预约会议根据会议开始时间来获取会议结束时间
     */
    public function getEndTime($uid,$arr){
        $starts = $arr['start'];
        $day =date('Y-m-d',strtotime($starts));
        $id=intval($arr['id']);

        $start = Meeting::where('start','<=',$starts)->where('end','>',$starts)->where('mr_id',$id)->where('day','=',$day)->where('status','=',2)->value('start');
        if($start){
            return returnJson($message='开始时间以被预约!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $start = Meeting::where('start','>',$starts)->where('end','>=',$starts)->where('mr_id',$id)->where('day','=',$day)->where('status','=',2)->orderBy('start','asc')->value('start');
        if(!$start){
            $endtime = MeetingRoom::where('id',$id)->value('end');
            $start=$day.' '.$endtime.':00';
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$start);
    }
    /*
     * 4-29
     * gaolu
     * 新建会议室设备配置列表数据接口
     * Equipment 设备配置
     * meetingReminderTime  提醒时间
     */
    public function getConfigureList($uid,$arr){
        $code=trim($arr['code']);
        $oaType = BasicOaType::where(['code'=>$code,'status'=>1])->select('id','title')->first();
        if ($oaType) {
            $data =BasicOaOption::where('type_id',$oaType->id)->get(['title','id'] )->toArray();
        }
        return returnJson($message='ok',$code=ConstFile::API_RESPONSE_SUCCESS,$data=$data?$data:[]);
    }

    //2019-04-27
    // 创建或更新申请单
    private function updateOrCreateEntry($data, $id = 0,$file_source)
    {
        $data['file_source_type'] = 'workflow';
        $data['file_source'] =$file_source;
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
        if (!empty($data['is_draft'])) {
            $entry->status = Entry::STATUS_DRAFT;
        } else {
            $entry->status = Entry::STATUS_IN_HAND;
        }
        return $entry;
    }


    /*
     * 4-26
     * gaolu
     * 会议添加
     */
    public function meetingAdd($uid,$arr,$id=0){
        if(empty($arr['mr_id'])){
            return returnJson($message='会议室不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['title'])){
            return returnJson($message='会议名称不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['describe'])){
            return returnJson($message='会议描述不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['host_id'])){
            return returnJson($message='主持人不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        if(empty($arr['day'])){
            return returnJson($message='日期不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if($arr['day']<date('Y-m-d',time())){
            return returnJson($message='过去日期不能预约！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        if(empty($arr['start'])){
            return returnJson($message='开始时间不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if($arr['start']<date('Y-m-d H:i:s',time())){
            return returnJson($message='过去时间不能预约！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        if(empty($arr['end'])){
            return returnJson($message='结束时间不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }


        if(empty($arr['remind'])){
            return returnJson($message='提示设置不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['send_type'])){
            return returnJson($message='发送方式不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if($arr['repeat_type']!=0){
            if(empty($arr['repeat_type'])){
                return returnJson($message='重复状态不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }

        if(!empty($arr['meeting_file']) && isset($arr['meeting_file'])){
            $data['meeting_file']=implode('|',$arr['meeting_file']);//会议文件
        }
        if(empty($arr['participant'])){
            return returnJson($message='参与人不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $copyperson=[];//抄送人
        if(!empty($arr['copyperson']) && isset($arr['copyperson'])){
            $copyperson=$arr['copyperson'];
        }
        $task=[];//任务
        if(!empty($arr['task']) && isset($arr['task'])){
            $task = $arr['task'];
        }

        $wheress['id']=intval($arr['mr_id']);
        $position=MeetingRoom::where($wheress)->value('position');
        if(!$position){
            return returnJson($message='会议室不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $where[]=['start','<=',$arr['start']];
        $where[]=['end','>',$arr['start']];
        $where['day']=trim($arr['day']);
        $where[]=['status','=',Meeting::API_STATUS_SUCCESS];
        $wheres[]=['start','<',$arr['end']];
        $wheres[]=['end','>=',$arr['end']];
        $wheres['day']=trim($arr['day']);
        $wheres[]=['status','=',Meeting::API_STATUS_SUCCESS];

        $count = Meeting::where($where)->count('id');
        if($count){
            return returnJson($message='开始时间不能被预约！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $count = Meeting::where($wheres)->count('id');
        if($count){
            return returnJson($message='结束时间不能被预约！',$code=ConstFile::API_RESPONSE_FAIL);

        }
        $start=strtotime($arr['start']);
        $end=strtotime($arr['end']);
        $duration=$end-$start;
        if($duration){

            $durationStr='';
            $days = floor($duration/ (60*60*24));
            if($days){
                $durationStr.=$days.'天';
            }
            $hours = floor(($duration - $days*60*60*24)  / (60*60));
            if($hours){
                $durationStr.=$hours.'小时';
                $minutes = floor(($duration - $days*60*60*24)  / ($hours*60*60)/ 60);
            }else{
                $minutes = floor($duration / 60);
            }

            if($minutes){
                $durationStr.=$minutes.'分钟';
            }
            $data['duration']=$durationStr;
        }
        $data['code']=getCode();
        $data['mr_id']=intval($arr['mr_id']);
        $data['position']=$position;
        $data['title']=trim($arr['title']);
        $data['describe']=trim($arr['describe']);
        $data['user_id']=$uid;
        $data['host_id']=intval($arr['host_id']);
        $data['start']=$arr['start'];
        $data['end']=$arr['end'];
        $data['day']=$arr['day'];
        $data['remind']=intval($arr['remind']);
        $describe=BasicOaOption::where('id',$data['remind'])->value('describe');
        if($describe){
            $data['deadline']=date('Y-m-d H:i:s',strtotime($data['start'])-($describe*60));
        }

        $data['number']=count($arr['participant']);
        $data['repeat_type']=intval($arr['repeat_type']);
        if($data['repeat_type']==0){// 提醒重复添加重复时间
            $data['repetition_time']=intval($arr['repetition_time']);
        }
        $data['send_type']=intval($arr['send_type']);
        $data['status']=1;
        $data['created_at']=date('Y-m-d H:i:s',time());
        //var_dump($data);die;
        try{
            DB::transaction(function() use($data,$arr,$copyperson,$task,$id, $uid) {
                $dataOne['title']=$arr['title'];

                $entry = FlowCustomize::EntryFlow($dataOne, 'meeting_record_review');//添加会议流程

                $data['entrise_id'] = $entry->id;
                $ids = Meeting::insertGetId($data);
                //添加参与人
                $participant= $arr['participant'];
                $data=[];
                $dataArr=[];
                $participate_name='';
                foreach ($participant as $k=>$v){
                    $data['user_id']=$v;
                    $data['chinese_name']=User::where('id',$v)->value('chinese_name');
                    $data['m_id']=$ids;
                    $data['type']=0;//参与人
                    $data['created_at']=date('Y-m-d H:i:s');
                    $dataArr[]=$data;
                    if($k==0){
                        $participate_name= $data['chinese_name'];
                    }else{
                        $participate_name=$participate_name.','.$data['chinese_name'];
                    }
                }
                //抄送人
                if($copyperson){
                    $data=[];
                    foreach ($copyperson as $k=>$vs){
                        $data['user_id']=$vs;
                        $data['chinese_name']=User::where('id',$vs)->value('chinese_name');
                        $data['m_id']=$ids;
                        $data['type']=1;//抄送人
                        $data['created_at']=date('Y-m-d H:i:s',time());
                        $dataArr[]=$data;
                    }
                }
                MeetingParticipant::insert($dataArr);
                //任务
                if($task){
                    $data=[];
                    $dataArr=[];
                    foreach ($task as $ks=>$vs){
                        User::where('id',intval($vs['user_id']))->value('chinese_name');
                        $data['user_id']=intval($vs['user_id']);
                        $data['chinese_name']=User::where('id',intval($vs['user_id']))->value('chinese_name');
                        $data['count']=trim($vs['count']);
                        $data['end']=$vs['end'];
                        $data['m_id']=$ids;
                        $data['created_at']=date('Y-m-d H:i:s',time());
                        $dataArr[]=$data;
                    }
                    MeetingTask::insert($dataArr);
                }
            });
            return returnJson($message = '添加成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }
    /*
     * 4-26
     * gaolu
     * 我添加的会议列表
     */
    public function getMeetingList($uid,$arr){
        $where['user_id']=$uid;
        if(!empty($arr['title']) && isset($arr['title'])){
            $where[] = ['title','like','%'.$arr['title'].'%'];
        }
        if(!empty($arr['start']) && isset($arr['start'])){
            $end=date('Y-m-d',strtotime($arr['start'])).' 23:59:59';
            $where[]=['start','>=',$arr['start']];
            $where[]=['end','<=',$end];

            $wherest[]=['start','<',$arr['start']];
            $wherest[]=['end','>',$arr['start']];
            $wherests[]=['start','<',$end];
            $wherests[]=['end','>',$end];


            $list = Meeting::where($where)->orWhere(function ($query) use($arr,$wherest,$wherests){
                    $query->orWhere($wherest)->orWhere($wherests);
                }) ->orderBy('id','desc')->select(['id','title','code','start','end','position','status'])->paginate(10);

        }else{
            $list = Meeting::where($where)->orderBy('id','desc')->select(['id','title','code','start','end','position','status'])->paginate(10);
        }
        if($list){
            $list=$list->toArray();
            unset($list['first_page_url']);
            unset($list['from']);
            unset($list['last_page_url']);
            unset($list['next_page_url']);
            unset($list['path']);
            unset($list['prev_page_url']);
            $list['new_date']=date('Y-m-d H:i:s',time());
        }else{
            $list=[];
        }
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,$data=$list);
    }

    /*
     * 4-26
     * gaolu
     * 我参加的会议列表
     */
    public function getPlusList($uid,$arr){
        //var_dump($uid);die;
        if(!empty($arr['title']) && isset($arr['title'])){
            $where[] = ['b.title', 'like', '%' . $arr['title'] . '%'];
        }
        $where['meeting_participant.type'] = 0;
        $where['meeting_participant.user_id']=$uid;
        $where[]=['b.status','=',Meeting::API_STATUS_SUCCESS];
        if(!empty($arr['start']) && isset($arr['start'])) {
            $where[] = ['b.start', '>=', $arr['start']];
            $where[] = ['b.end', '<=', $arr['end']];

            $wherest[] = ['b.start', '<', $arr['start']];
            $wherest[] = ['b.end', '>', $arr['start']];
            $wherests[] = ['b.start', '<', $arr['end']];
            $wherests[] = ['b.end', '>', $arr['end']];
            $list = Meeting::where($where)->orWhere(function ($query) use ($arr, $wherest, $wherests) {
                $query->orWhere($wherest)->orWhere($wherests);
            }) ->orderBy('meeting_participant.id','desc')->select(['b.id','b.title','b.code','b.start','b.end','b.position','b.status'])->paginate(10);
        }else{
            $list = MeetingParticipant::leftJoin('meeting as b','meeting_participant.m_id','=','b.id')->where($where)
                ->orderBy('meeting_participant.id','desc')
                ->select(['b.id','b.title','b.code','b.start','b.end','b.position','b.status'])
                ->paginate(10);
        }
//        $list = MeetingParticipant::with(['lists'=>function ($query) use ($wheres){
//            $query->where($wheres)->get(['id','title','code','start','end','position','status']);
//        }])->where($where)->orderBy('id','desc')->get(['m_id'])->paginate(10)->toArray();

        if($list){
            $list=$list->toArray();
            unset($list['first_page_url']);
            unset($list['from']);
            unset($list['last_page_url']);
            unset($list['next_page_url']);
            unset($list['path']);
            unset($list['prev_page_url']);
        }else{
            $list=[];
        }
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,$data=$list);
    }

    /**
     * 会议撤回
     */

    public function withdraw($user,$arr){
        $where['user_id']=$user->id;
        $where['status']=Meeting::API_STATUS_EXAMINE;
        if(empty($arr['id'])){
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $data['status']=Meeting::API_STATUS_REVOKE;
        $data['updated_at']=date('Y-m-d H:i:s',time());
        try{
            $entrise_id= Meeting::where($where)->value('entrise_id');
            $n=$this->cancel($entrise_id,'',$user->id);//流的撤销
            if(!$n){
                return returnJson($message = '已有审核记录，不能撤销操作', $code = ConstFile::API_RESPONSE_FAIL);
            }
            Meeting::where($where)->update($data);
            return returnJson($message = '撤销成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     * 撤销
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function cancel($entry_id,$content='',$uid){

        $entryObj = Entry::find($entry_id);
        //已经有审核的记录了 不让撤销
        $cnt = Proc::query()->where('entry_id', '=', $entry_id)
            ->where('status', '=', Proc::STATUS_PASSED)
            ->where('user_id', '!=', $uid)
            ->count();
        if($cnt > 0){
            return 0;
        }
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
        $res = $entryObj->save();
        //
        return $res;
    }

    /*
     * 4-27
     * gaolu
     * 审核通过
     */
    public function passMeeting($arr)
    {

        try {
            DB::transaction(function () use ($arr) {
                (new Workflow())->passWithNotify($arr['id']);
            });
            return returnJson($message = '审核成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($message = $this->message, $this->code);
        }
    }

    /*
     * 4-27
     * gaolu
     * 审核不通过
     */
    public function rejectMeeting($arr)
    {
        if(!empty($arr['content']) && isset($arr['content'])){
            $content=$arr['content'];
        }else{
            $content='';
        }
        try {
            DB::transaction(function () use ($arr,$content) {
                (new Workflow())->reject(intval($arr['id']), $content);
            });
            return returnJson($message = '拒绝成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($message = $this->message, $this->code);
        }
    }
    /*
     * 4-27
     * gaolu
     * 会议审核人的详情接口
     */
    public function meetingReviewedInfo($uid,$arr){
       $id = intval($arr['id']);
       if(!$id){
           return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
       }
       $where['id']=$id;
       $where['user_id']=$uid;
       //var_dump($where);die;
       $entry_id = Proc::where($where)->value('entry_id');
       if(!$entry_id){
           return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
       }
       $wheres['entrise_id']=$entry_id;
        //var_dump($wheres);die;
        $info = Meeting::with(['getTask'])->where($wheres)->orwhere('summary_id',$entry_id)
            ->first(['id','title','describe','start','end','meeting_file','meeting_summary','host_id','number','status','created_at','code','position','entrise_id','summary_id']);
        if($info){
            $info = $info->toArray();
            $info['host_name'] = User::where('id',$info['host_id'])->value('chinese_name');

            $end = strtotime($info['end']);
            $time=time();
            if($end<$time){
                $info['complete']=1;
                if($info['summary_id']){
                    $entry = Entry::findOrFail($info['summary_id']);
                    $info['process'] = $this->fetchEntryProcess($entry);
                }else{
                    $info['process']=[];
                }
            }else{
                $info['complete']=0;
                $entry = Entry::findOrFail($info['entrise_id']);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
           $info['type']=2;
           //会议参与人
           $info['wp_id']=$id;
           $whereA['m_id']=$info['id'];
           $info['participate_name']='';
           $n = MeetingParticipant::where($whereA)->where('type',0)->get(['chinese_name','signin']);
           if($n){
               $get_participant=$n->toArray();

               $name='';
               $name1='';
               $name2='';
               $num1=0;
               $num2=0;
               foreach($get_participant as $key =>$va){
                    if($key==0){
                        $name=$va['chinese_name'];
                    }else{
                        $name.=','.$va['chinese_name'];
                    }
                    if($va['signin']==1){
                        $num1=$num1+1;
                        if(!$name1){
                            $name1= $va['chinese_name'];
                        }else{
                            $name1.=','.$va['chinese_name'];
                        }
                    }else{
                        $num2=$num2+1;
                        if(!$name2){
                            $name2= $va['chinese_name'];
                        }else{
                            $name2.=','.$va['chinese_name'];
                        }
                    }
               }
               $info['participate_name']=$name;
               $info['see_name']=$name1;
               $info['unsee_name']=$name2;
               $info['see_num']=$num1;
               $info['unsee_num']=$num2;

           }
           $info['copy_person']='';
           $info['copy_person_num']=0;
           //会议抄送人
           $n = MeetingParticipant::where($whereA)->where('type',1)->get(['chinese_name','signin']);
           if($n){
               $name='';
               foreach($n->toArray() as $key =>$va){
                   if($key==0){
                       $name=$va['chinese_name'];
                   }else{
                       $name.=','.$va['chinese_name'];
                   }
               }
               $info['copy_person']=$name;
               $info['copy_person_num']=count($n->toArray());
           }
           $info['new_date']=date('Y-m-d H:i:s',time());
           $is_status = Proc::where('id',$id)->value('status');
           $info['is_status']=$is_status;
           //
       }else{
           $info=[];
       }
       return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$info);

    }


    /*
     * 4-28
     * gaolu
     * 参与人的详情接口
     */
    public function meetingInfo($uid,$arr){
        //var_dump($uid);die;
        $id = intval($arr['id']);
        if(!$id){
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['m_id']=$id;
        $where['user_id']=$uid;

        $n=MeetingParticipant::where($where)->orderBy('type','asc')->first(['id','type','signin']);
        if(!$n){
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $wheres['id']=$id;
        $where['status']=2;

        $info = Meeting::with(['getTask'])->where($wheres)
            ->first(['id','title','describe','start','end','meeting_file','meeting_summary','host_id','number','status','created_at','code','position','entrise_id','summary_id']);
        if($info){

            $info = $info->toArray();
            $info['host_name'] = User::where('id',$info['host_id'])->value('chinese_name');
            $end = strtotime($info['end']);
            $time=time();
            if($end<$time){
                $info['complete']=1;
                if($info['summary_id']){
                    $entry = Entry::findOrFail($info['summary_id']);
                    $info['process'] = $this->fetchEntryProcess($entry);
                }else{
                    $entry = Entry::findOrFail($info['entrise_id']);
                    $info['process'] = $this->fetchEntryProcess($entry);
                }
            }else{
                $info['complete']=0;
                $entry = Entry::findOrFail($info['entrise_id']);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
            $info['type']=$n->type;
            $info['type']=$n->signin;//签到状态
            $info['host_name']=User::where('id',$info['host_id'])->value('chinese_name');//签到状态

            //会议参与人
            $whereA['m_id']=$info['id'];
            $info['participate_name']='';
            $n = MeetingParticipant::where($whereA)->where('type',0)->get(['chinese_name','signin']);
            if($n){
                $get_participant=$n->toArray();

                $name='';
                $name1='';
                $name2='';
                $num1=0;
                $num2=0;
                foreach($get_participant as $key =>$va){
                    if($key==0){
                        $name=$va['chinese_name'];
                    }else{
                        $name.=','.$va['chinese_name'];
                    }
                    if($va['signin']==1){
                        $num1=$num1+1;
                        if(!$name1){
                            $name1= $va['chinese_name'];
                        }else{
                            $name1.=','.$va['chinese_name'];
                        }
                    }else{
                        $num2=$num2+1;
                        if(!$name2){
                            $name2= $va['chinese_name'];
                        }else{
                            $name2.=','.$va['chinese_name'];
                        }
                    }
                }
                $info['participate_name']=$name;
                $info['see_name']=$name1;
                $info['unsee_name']=$name2;
                $info['see_num']=$num1;
                $info['unsee_num']=$num2;

            }
            $info['copy_person']='';
            $info['copy_person_num']=0;
            //会议抄送人
            $n = MeetingParticipant::where($whereA)->where('type',1)->get(['chinese_name','signin']);
            if($n){
                $name='';
                foreach($n->toArray() as $key =>$va){
                    if($key==0){
                        $name=$va['chinese_name'];
                    }else{
                        $name.=','.$va['chinese_name'];
                    }
                }
                $info['copy_person']=$name;
                $info['copy_person_num']=count($n->toArray());
            }
        } else{
            $info=[];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$info);
    }
    /*
     * 4-27
     * gaolu
     * 我申请的会议的详情接口
     */
    public function meetingMeInfo($uid,$arr){
        if(!empty($arr['id']) && isset($arr['id'])){
            $id = intval($arr['id']);
            $where['id']=$id;
        }
        if(!empty($arr['entrise_id']) && isset($arr['entrise_id'])){
            $id = intval($arr['entrise_id']);
            $where['entrise_id']=$id;
        }
        //$where['user_id']=$uid;
        //var_dump($wheres);die;
        $info = Meeting::with(['getTask'])->where($where)
            ->first(['id','title','describe','start','end','meeting_file','meeting_summary','host_id','number','status','created_at','code','position','entrise_id','summary_id']);
        if($info){
            $info = $info->toArray();
            $info['host_name'] = User::where('id',$info['host_id'])->value('chinese_name');
            $end = strtotime($info['end']);

            $time=time();
            if($end<$time){
                $info['complete']=1;
                if($info['summary_id']){
                    $entry = Entry::findOrFail($info['summary_id']);
                    $info['process'] = $this->fetchEntryProcess($entry);
                }else{
                    $info['process']=[];
                }
            }else{
                $info['complete']=0;
                $entry = Entry::findOrFail($info['entrise_id']);
                $info['process'] = $this->fetchEntryProcess($entry);
            }

            $info['type']=2;
            //会议参与人
            $whereA['m_id']=$info['id'];
            $info['participate_name']='';
            $n = MeetingParticipant::where($whereA)->where('type',0)->get(['chinese_name','signin']);
            if($n){
                $get_participant=$n->toArray();

                $name='';
                $name1='';
                $name2='';
                $num1=0;
                $num2=0;
                foreach($get_participant as $key =>$va){
                    if($key==0){
                        $name=$va['chinese_name'];
                    }else{
                        $name.=','.$va['chinese_name'];
                    }
                    if($va['signin']==1){
                        $num1=$num1+1;
                        if(!$name1){
                            $name1= $va['chinese_name'];
                        }else{
                            $name1.=','.$va['chinese_name'];
                        }
                    }else{
                        $num2=$num2+1;
                        if(!$name2){
                            $name2= $va['chinese_name'];
                        }else{
                            $name2.=','.$va['chinese_name'];
                        }
                    }
                }
                $info['participate_name']=$name;
                $info['see_name']=$name1;
                $info['unsee_name']=$name2;
                $info['see_num']=$num1;
                $info['unsee_num']=$num2;

            }
            $info['copy_person']='';
            $info['copy_person_num']=0;
            //会议抄送人
            $n = MeetingParticipant::where($whereA)->where('type',1)->get(['chinese_name','signin']);
            if($n){
                $name='';
                foreach($n->toArray() as $key =>$va){
                    if($key==0){
                        $name=$va['chinese_name'];
                    }else{
                        $name.=','.$va['chinese_name'];
                    }
                }
                $info['copy_person']=$name;
                $info['copy_person_num']=count($n->toArray());
            }
            $info['new_date']=date('Y-m-d H:i:s',time());
            $is_status = Proc::where('user_id',$uid)->where('entry_id',$info['entrise_id'])->value('id');
            $info['proc_id']=$is_status;
            //
        }else{
            $info=[];
        }

        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$info);

    }
    /**
     * 4-28
     * gaolu
     * 会议签到
     * id会议id
     */
    public function setMeetingSigin($uid,$arr)
    {
        $id=intval($arr['id']);
        if(!$id){
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['m_id']=$id;
        $where['user_id']=$uid;
        $where['type']=0;
        $signin = MeetingParticipant::where($where)->value('signin');

        if($signin==0){
            $data['signin']=1;
            $data['updated_at']=date('Y-m-d H:i:s',time());
            $n = MeetingParticipant::where($where)->update($data);
            if($n){
                return returnJson($message = '签到成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }
            return returnJson($message = '签到失败', $code = ConstFile::API_RESPONSE_FAIL);
        }else{
            $data['signin']=2;
            $data['updated_at']=date('Y-m-d H:i:s',time());
            $n = MeetingParticipant::where($where)->update($data);
            if($n){
                return returnJson($message = '签退成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }
            return returnJson($message = '签退失败', $code = ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     * 4-28
     * gaolu
     * 会议任务列表
     * id会议id
     */
    public function getTaskList($uid,$arr)
    {
        if(!empty($arr['id']) && isset($arr['id'])) {
            $id = intval($arr['id']);
            $where['m_id']=$id;
        }

        if(!empty($arr['entrise_id']) && isset($arr['entrise_id'])){
            $id = intval($arr['entrise_id']);
            $wheres['entrise_id']=$id;
            $id = Meeting::where($wheres)->value('id');
            $where['m_id']=$id;
        }
        $where['status']=1;
        $n = MeetingTask::where($where)->get(['chinese_name','count','end']);
        if(!$n){
            $n=[];
        }else{
          $n = $n->toArray();
        }
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,$n);
    }

    /**
     * 4-29
     * gaolu
     * 脚本跑代码
     * 会议截止时间提醒
     */
    public function remindTime($uid=0)
    {
        $where['day']=date('Y-m-d');//日期
        $where['deadline']=date('Y-m-d H:i:00');//时间
        $where[]=['status','=',Meeting::API_STATUS_SUCCESS];//时间
        //var_dump($where);die;
        //不重复提醒的数据
        $n = Meeting::where($where)->get(['id','title','start','send_type','repeat_type','deadline','user_id','repetition_time']);
        //var_dump($n);die;
        if($n){
            $list=$n->toArray();
            foreach ($list as $key=>$val){
                $content="您的会议[".$val['title']."]将于[".$val['start']."开始！请准时到达现场，特殊原因请提前说明！";
                $uid = MeetingParticipant::where('m_id',$val['id'])->where('type',0)->get(['user_id'])->toArray();
                if($val['send_type']==0){//发送短信提示
                    foreach($uid as $k=>$v){
                        $mobile = User::where('id',$v['user_id'])->value('mobile');
                        //应用消息发送接口
                    }
                }else{//发送应用提示
                    foreach($uid as $k=>$v){
                        $mobile = User::where('id',$v['user_id'])->value('mobile');
                        //应用消息发送接口
                        $dataOne = [
                            'receiver_id' => $v['user_id'],//接收者（申请人）
                            'sender_id' => $n['user_id'],//发送者（最后审批人）
                            'content'=> $content,//内容
                            'type' => Message::MESSAGE_TYPE_USER_MEETING,
                            'relation_id' => $n['id'],//workflow_entries 的 id
                            'created_at'=>date('Y-m-d H:i:s',time()),
                            'updated_at'=>date('Y-m-d H:i:s',time())
                        ];
                        $datas[]=$dataOne;
                    }
                    Message::insert($datas);
                }
                if($val['repeat_type']==0){//重复状态 0重复 1不重复
                    $date['deadline'] = date('Y-m-d H:i:00',strtotime($val['deadline'])+($val['repetition_time']*60));
                    $date['updated_at']=date('Y-m-d H:i:s',time());
                    $wheres['id']=$val['id'];
                    Meeting::where($wheres)->update($date);
                }
                //var_dump($val);
            }
        }
    }

    /**
     * 4-29
     * gaolu
     * 发布人给未签到的人进行提醒
     */
    public function setNosigninRemind($uid,$arr)
    {
        $where['id']=intval($arr['id']);//日期
        $where[]=['status','=',Meeting::API_STATUS_SUCCESS];//时间
        //var_dump($where);die;
        //不重复提醒的数据
        $n = Meeting::where($where)->first(['id','title','start','send_type','user_id','repeat_type','deadline','repetition_time']);
        if($n){
            $val=$n->toArray();
            $content="您的会议[".$val['title']."]将于[".$val['start']."开始！请准时到达现场，特殊原因请提前说明！";
            $uid = MeetingParticipant::where('m_id',$val['id'])->where('type',0)->where('signin',0)->get(['user_id'])->toArray();
            if($val['send_type']==0){//发送短信提示
                foreach($uid as $k=>$v){
                    $mobile = User::where('id',$v['user_id'])->value('mobile');
                    //给每个人发短信
                    var_dump($v['user_id']);
                }
            }else{//发送应用提示
                foreach($uid as $k=>$v){
                    $mobile = User::where('id',$v['user_id'])->value('mobile');
                    //给每个人发应用消息
                    //应用消息发送接口
                    $dataOne = [
                        'receiver_id' => $v['user_id'],//接收者（申请人）
                        'sender_id' => $n['user_id'],//发送者（最后审批人）
                        'content'=> $content,//内容
                        'type' => Message::MESSAGE_TYPE_USER_MEETING,
                        'relation_id' => $n['id'],//workflow_entries 的 id
                        'created_at'=>date('Y-m-d H:i:s',time()),
                        'updated_at'=>date('Y-m-d H:i:s',time())
                    ];
                    $datas[]=$dataOne;
                }
                Message::insert($datas);
            }
            return returnJson($message = '提醒成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message = '会议开始不能进行消息提示哦！', $code = ConstFile::API_RESPONSE_FAIL);
    }

    /**
     * 4-28
     * gaolu
     * 工作流申请审核人的状态数据转换
     */
    public function fetchEntryProcess(Entry $entry)
    {

        $processes = (new Workflow())->getProcs($entry);
        //dd($processes->toArray());die;
        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $status=$entry->status;
        $processAuditors = $temp = [];

        foreach ($processes as $key=> $process) {//print_r($process);die;$temp['process_name'] = $process->process_name;
            $temp['process_name'] = $process->process_name;
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $process->proc ? $process->proc->content : '';

            if ($process->proc && $process->proc->auditor_name) {
                $temp['auditor_name'] = $process->proc->auditor_name;
                $temp['auditor_id'] = $process->proc->auditor_id;
            } elseif ($process->proc && $process->proc->user_name) {
                $temp['auditor_name'] = $process->proc->user_name;
                $temp['auditor_id'] = $process->proc->user_id;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status'] = $process->proc ? $process->proc->status : '';
            $temp['updated_at'] = ($process->proc && $temp['status'] != Proc::STATUS_IN_HAND) ? $process->proc->updated_at : '';

            $temp['status_name'] = '';
            if($status==-2){
                if ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                    $temp['status_name'] = '已撤回';
                } else {
                    $temp['status_name'] = '已完成';
                }
            }else{
                if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                    $temp['status_name'] = '驳回';
                } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                    $temp['status_name'] = '完成';
                } else {
                    $temp['status_name'] = '待处理';
                }
            }
            $processAuditors[] = $temp;
        }

        return $processAuditors;
    }

    /**
     * 会议纪要添加工作工作流
     */
    public function setSummary($user,$arr)
    {
        if(empty($arr['id'])){
            return returnJson($message = '会议参数错误', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['count'])){
            return returnJson($message = '会议纪要参数不能为空!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $where['user_id']=$user->id;
        $info =  Meeting::where($where)->first(['id','title']);
        if($info){
            $info=$info->toArray();
            $data['meeting_summary']=trim($arr['count']);
            DB::transaction(function() use($data,$where,$info) {
                $dataOne['title'] = '会议纪要审核';
                $entry = FlowCustomize::EntryFlow($dataOne, 'meeting_summary');//添加会议纪要工作流
                $data['summary_id'] = $entry->id;
                Meeting::where($where)->update($data);
            });
            return returnJson($message = '会议纪要添加成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }else{
            return returnJson($message = '会议纪要添加失败', $code = ConstFile::API_RESPONSE_FAIL);
        }

    }

    /**
     * 公章管理元获取公章类型列表
     *
     */
    public function getSealsType($user){
        $list = SealsType::getlist($user);
        if($list){
            $list=$list->toArray();
        }
        $list=[];
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$list);
    }

    /**
     * 公章管理元获取公章列表
     *
     */
    public function getSeals($user,$arr){
        if(empty($arr['id'])){
            return returnJson($message = '参数错误', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $list = Seals::getSeals(intval($arr['id']));
        if($list){
            $list=$list->toArray();
        }
        $list=[];
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$list);
    }
}
