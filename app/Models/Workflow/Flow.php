<?php

namespace App\Models\Workflow;

use App\Http\Helpers\Dh;
use App\Http\Helpers\StringHelper;
use App\Models\Financial;
use Illuminate\Database\Eloquent\Model;
use UserFixException;
/**
 * Class Flow
 *
 * @package App\Models\Workflow
 * @method static \Illuminate\Database\Eloquent\Builder|static publish()
 * @method static \Illuminate\Database\Eloquent\Builder|static show()
 * @method static \Illuminate\Database\Eloquent\Builder|static unabandon()
 * @property int $id
 * @property string $flow_no
 * @property string $flow_name
 * @property int $template_id
 * @property int $type_id
 * @property string $flowchart
 * @property string $jsplumb
 * @property int $is_publish
 * @property int $is_show
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $leader_link_type 领导人审批条线,business:业务条线;report:汇报关系条线
 * @property int $version
 * @property int $is_abandon
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\Process[] $process
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\ProcessVar[] $process_var
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\ProcessVar[] $flow_link
 * @property-read \App\Models\Workflow\Template $template
 * @property-read \App\Models\Workflow\FlowType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereFlowName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereFlowNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereFlowchart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereIsPublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereJsplumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereLeaderLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $introduction 流程说明
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereIntroduction($value)
 * @property int $create_user_id 创建人Id
 * @property int $clone_from_flow_id 克隆自哪只流程
 * @property string|null $can_view_users 能看到本流程的用户id集合
 * @property string|null $can_view_departments 能看到本流程的部门id集合
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereCanViewDepartments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereCanViewUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereCloneFromFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereIsAbandon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flow whereVersion($value)
 */
class Flow extends Model
{
    // 领导人审批条线
    const FLOW_LEADER_LINK_BUSINESS = 'business'; // 业务条线
    const FLOW_LEADER_LINK_REPORT   = 'report'; // 汇报关系条线

    const FLOW_LEADER_LINK_MAP = [
        self::FLOW_LEADER_LINK_BUSINESS => '业务',
        self::FLOW_LEADER_LINK_REPORT   => '汇报关系',
    ];

    const PUBLISH_YES = 1; // 已发布
    const PUBLISH_NO  = 0; // 未发布

    const ABANDON_YES = 1; // 已废弃
    const ABANDON_NO  = 0; // 未废弃

    const TYPE_PEOPLE = 1; //人事
    const TYPE_MONEY = 2; //财务
    const TYPE_ATTENTION = 3; // 考勤
    const TYPE_ADMINISTRATIVE = 4;  //行政

    protected $table = "workflow_flows";

    protected $fillable = ['flow_no', 'flow_name', 'template_id', 'flowchart', 'jsplumb', 'is_publish', 'is_show',
        'type_id', 'leader_link_type','introduction','can_view_users','can_view_departments','icon_url','route_url','show_route_url'];

    public function process()
    {
        return $this->hasMany('App\Models\Workflow\Process', 'flow_id');
    }

    public function process_var()
    {
        return $this->hasMany('App\Models\Workflow\ProcessVar', 'flow_id');
    }

    public function flow_link()
    {
        return $this->hasMany('App\Models\Workflow\Flowlink', 'flow_id');
    }

    public function template()
    {
        return $this->belongsTo('App\Models\Workflow\Template', 'template_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Workflow\FlowType', 'type_id');
    }

    public function scopePublish($scope)
    {
        return $scope->where('is_publish', 1);
    }

    public function scopeShow($scope)
    {
        return $scope->where('is_show', 1);
    }

    /**
     * 未废弃的 发布的流程
     * @param $scope
     *
     * @return mixed
     *
     */
    public static function findEffectiveFlowByFlowNo($flow_no)
    {
        return static::where('flow_no', $flow_no)
            ->where('is_abandon', self::ABANDON_NO)
            ->where('is_publish', self::PUBLISH_YES)
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * 未废弃的
     * @param $scope
     *
     * @return mixed
     *
     */
    public function scopeUnabandon($scope)
    {
        return $scope->where('is_abandon', self::ABANDON_NO);
    }

    /**
     * 根据流程编号获取该流程最新流程
     * @param $flow_no
     * @return \Illuminate\Database\Eloquent\Collection|static
     * @author hurs
     */
    public static function findByFlowNo($flow_no)
    {
        return static::where('flow_no', $flow_no)->publish()->show()->orderBy('id', 'DESC')->first();
    }

    /**
     * 根据流程编号获取该流程最新流程,不管显示不显示
     * @param $flow_no
     * @return \Illuminate\Database\Eloquent\Collection|static
     * @author hurs
     */
    public static function findByFlowNoAnyWay($flow_no)
    {
        return static::where('flow_no', $flow_no)->publish()->orderBy('id', 'DESC')->first();
    }

    /**
     * 根据流程编号获取该流程历史全部流程
     * @param $flow_no
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function findAllByFlowNo($flow_no)
    {
        return static::with('template.template_form')->where('flow_no', $flow_no)->get();
    }

    /**
     * 根据流程编号获取该流程最新流程
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function findFlowNos()
    {
        return static::publish()->show()->orderBy('id', 'DESC')->get();
    }

    public static function getFlows()
    {
        return static::orderBy('id')->get();
    }

    /**
     * 获取编号对应的流程名
     * @return array
     */
    public static function getFlowsOfNo()
    {
        $res = [];
        $flows = static::getFlows();
        foreach ($flows as $workflow_flow) {
            $res[$workflow_flow->flow_no] = $workflow_flow->flow_name;
        }

        return $res;
    }

    /**
     * 获取编号对应的流程名
     * @return array
     */
    public static function getFlowsOfNoCorporate()
    {
        $res = [];
        $flows = static::getFlows();
        foreach ($flows as $workflow_flow) {
            $res[$workflow_flow->flow_no] = ['id'=>$workflow_flow->id,'flow_name'=>$workflow_flow->flow_name];
        }


        return $res;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public static function findById($id)
    {
        return static::findOrFail($id);
    }

    /**
     * 检查是否可提交
     * @throws \Exception
     */
    public function checkCanApply()
    {
        if ($this->is_publish != self::PUBLISH_YES) {
            throw new UserFixException('该流程未发布,或在修改中,暂不能申请');
        }

        if ($this->is_abandon == self::ABANDON_YES) {
            throw new UserFixException('该流程已废弃不再使用,不可以再申请');
        }
    }

    /**
     * 克隆流程,生成新版本
     * @param $id integer 原流程id
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function cloneFlow($id)
    {
        $data = static::with('template', 'template.template_form', 'process', 'process.process_var', 'flow_link')
            ->where('id', '=', $id)
            ->first();

        // 拷贝模板
        $templateClone = $data->template->toArray();
        unset($templateClone['id']);
        $templateClone['template_name'] = $templateClone['template_name'] . '_' . date('YmdHis');
        $templateClone['created_at']    = Dh::getcurrentDateTime();
        $templateClone['updated_at']    = Dh::getcurrentDateTime();
        $templateNew                    = Template::create($templateClone);

        // 拷贝模板的元素
        $templateFormClone = $data->template->template_form->toArray();
        array_walk($templateFormClone, function ($val) use ($templateNew) {
            unset($val['id']);
            $val['template_id'] = $templateNew->id;
            $val['created_at']  = Dh::getcurrentDateTime();
            $val['updated_at']  = Dh::getcurrentDateTime();
            TemplateForm::create($val);
        });

        // 落地拷贝的流程
        $flowClone = $data->attributesToArray();
        unset($flowClone['id']); // 删除id
        $flowClone['created_at']         = Dh::getcurrentDateTime();
        $flowClone['updated_at']         = Dh::getcurrentDateTime();
        $flowClone['is_publish']         = self::PUBLISH_NO;
        $flowClone['template_id']        = $templateNew->id;
        $flowNew                  = Flow::create($flowClone);

        // 流程节点
        $processClone     = $data->process->toArray();
        $processOldNewMap = []; // old_id => new _id
        array_walk($processClone, function ($val) use ($flowNew, &$processOldNewMap) {
            $oldId = $val['id'];
            unset($val['id']);
            $val['flow_id']    = $flowNew->id;
            $val['created_at'] = Dh::getcurrentDateTime();
            $val['updated_at'] = Dh::getcurrentDateTime();

            $processNew               = Process::create($val);
            $processOldNewMap[$oldId] = $processNew->id;
        });

        // 流程变量
        $processVarClone = $data->process_var->toArray();
        array_walk($processVarClone, function ($val) use ($flowNew, $processOldNewMap) {
            unset($val['id']);
            $oldProcessId      = $val['process_id']; // 对应的老的process
            $newProcessId      = $processOldNewMap[$oldProcessId]; // 新的process id
            $val['process_id'] = $newProcessId;
            $val['flow_id']    = $flowNew->id;
            ProcessVar::create($val);
        });

        // 节点流转
        $flowLinkClone = $data->flow_link->toArray();
        array_walk($flowLinkClone, function ($val) use ($flowNew, $processOldNewMap) {
            unset($val['id']);

            $val['flow_id']    = $flowNew->id;
            $oldProcessId      = $val['process_id']; // 对应的老的process
            $newProcessId      = $processOldNewMap[$oldProcessId]; // 新的process id
            $val['process_id'] = $newProcessId;

            $oldNextProcessId       = $val['next_process_id']; // 对应的老的next process
            $newNextProcessId       = $processOldNewMap[$oldNextProcessId] ?? $oldNextProcessId; // 新的next process id
            $val['next_process_id'] = $newNextProcessId;

            $val['created_at'] = Dh::getcurrentDateTime();
            $val['updated_at'] = Dh::getcurrentDateTime();

            Flowlink::create($val);
        });

        // 重新计算jsplumb并更新
        $jsplumb         = json_decode($flowNew->jsplumb, true);
        if (!empty($jsplumb)) {
            $jsplumb['list'] = array_map(function ($val) use ($flowNew, $processOldNewMap) {
                $val['id'] = $processOldNewMap[$val['id']];
                $val['flow_id'] = $flowNew->id; // 替换流程id
                // 替换process_to信息
                if (!StringHelper::isEmpty($val['process_to'])) {
                    $oldProcessTo = explode(',', $val['process_to']);
                    $newProcessTo = array_map(function ($oldProcessId) use ($processOldNewMap) {
                        return $processOldNewMap[$oldProcessId];
                    }, $oldProcessTo);

                    $val['process_to'] = implode(',', $newProcessTo);
                }

                return $val;
            }, $jsplumb['list']);
        }

        $flowNew->jsplumb = json_encode($jsplumb);
        $flowNew->version = time();
        $flowNew->save();

        return $flowNew;
    }

    /**
     * 设置流程废弃状态
     * @param $flow_id
     * @param $state
     *
     * @return bool
     */
    public static function setAbandonState($flow_id, $state)
    {
        $flow = static::findOrFail($flow_id);
        $flow->is_abandon = $state;
        return $flow->save();
    }

    /**
     * 通过flow_no获取flow_id 的数组
     * @param $flow_id
     * @param $state
     *
     * @return bool
     */
    public static function getFlowFinance($flow_nos)
    {
        $flows=Flow::where([
            'is_abandon' => self::ABANDON_NO,
            'is_publish' => self::PUBLISH_YES
        ])->whereIn('flow_no',$flow_nos)
            //->whereHas('finance')
            ->select('id','flow_no','flow_name')
           // ->with('finance')
            ->get()->toArray();
        return $flows;
    }
    //审批完的财务数据
    public function finance()
    {
        return $this->hasMany(Financial::class, 'flow_id','id')
            ->where('status',3)
            ->select('id as financial_id','flow_id','user_id','title','expense_amount');

    }

    public function entries(){
        return $this->hasMany(Entry::class, 'flow_id', 'id');
    }
    //通过编号数组获取id的数组
    public static function getFlowIds($flowNos){
        $ids=[];
        $flows=Flow::where([
            'is_abandon' => self::ABANDON_NO,
            'is_publish' => self::PUBLISH_YES
        ])->whereIn('flow_no',$flowNos)
            ->pluck('id');
        if($flows){
            $ids=$flows->toArray();
        }

        return $ids;
    }
}
