<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 10:47
 */

namespace App\Models\Contract;

use App\Constant\ConstFile;
use App\Models\Salary\Salary;
use App\Models\Salary\UsersSalary;
use App\Models\Salary\UsersSalaryRelation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers\Dh;

class Contract extends Model {

    use SoftDeletes;

    /**
     * @deprecated 合同审批状态
     */
    const CONTRACT_STATUS_ONE = 1;
    const CONTRACT_STATUS_TWO = 2;
    const CONTRACT_STATUS_THR = 3;
    public static $contractStatusMsg = [
        self::CONTRACT_STATUS_ONE => '未审核',
        self::CONTRACT_STATUS_TWO => '已审批',
        self::CONTRACT_STATUS_THR => '已拒绝',
    ];

    const CONTRACT_PROBATION_ONE = 1;// 1，无试用期
    const CONTRACT_PROBATION_TWO = 2;// 2，一个月
    const CONTRACT_PROBATION_THR = 3;// 3，三个月
    public static $probation = [
        self::CONTRACT_PROBATION_ONE => '无试用期',
        self::CONTRACT_PROBATION_TWO => '一个月',
        self::CONTRACT_PROBATION_THR => '三个月',
    ];
    public static $contractMonths = [
        self::CONTRACT_PROBATION_ONE => 0,
        self::CONTRACT_PROBATION_TWO => 1,
        self::CONTRACT_PROBATION_THR => 3,
    ];
    protected $table = 'contract';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'renew_count',
        'probation',
        'contract',
        'template_id',
        'template_name',
        'probation_ratio',
        'entry_at',
        'contract_end_at',
        'create_user_id',
        'user_name',
        'create_user_name',
        'version',
        'entry_id',
        'entrise_id',
        'salary_version',
    ];

    /**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        "id"
    ];
    protected $hidden = [
        'deleted_at'
    ];

    /**
     *
     * @return User
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * @description 薪资合同数据关联表
     * @author liushaobo
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function belongsToManyUserSalaryDataContract(){
        return $this->belongsToMany(UsersSalaryRelation::class,'users_salary_data','contract_id','relation_id')->select(['field_name','field_data','status']);
    }
    /**
     * @description 薪资合同数据关联表
     * @author liushaobo
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function belongsToManySalaryData(){
        return $this->belongsToMany(UsersSalaryRelation::class,'users_salary_data','contract_id','relation_id')->where('status',Salary::SALARY_RELATION_STATUS_SALARY)->select(['field_name','field_data','status']);
    }

    public function hasManySalaryRelation(){
        return $this->belongsToMany(UsersSalaryRelation::class,'users_salary_data','contract_id','relation_id')->where('status','<>',Salary::SALARY_RELATION_STATUS_BONUS)->select(['field_name','field_data','status']);
    }

    public function hasOneUsersSalary(){
        return $this->hasOne(UsersSalary::class,'contract_id','id');
    }


    /**
     * @param $params
     * @return bool
     */
    public static function workflowImport($params){
        if(is_array($params)){
            //无效数据不入库
            if (!isset($params['entrise_id']) ||
                !isset($params['status'])
            ) {
                return false;
            }
            $entrise_id = $params['entrise_id'];
            $contract = self::where('entrise_id', $entrise_id)->first();
            if($contract){
                $contract = new self();
                $contractData = array();
                $contractData['status'] = $params['status'];
                $contract->where('entrise_id', $entrise_id)->update($contractData);
            }
        }
    }

    public static function workflowUserContract($params){
        if(is_array($params)){
            //无效数据不入库
            if (!isset($params['entrise_id'])
            ) {
                return false;
            }
            $entrise_id = $params['entrise_id'];
            unset($params['entrise_id']);
            $contract = self::where('entrise_id', $entrise_id)->first();
            if($contract){
                $user = User::find($contract->user_id);
                if($user){
                    User::where('id',$contract->user_id)->update($params);
                }
            }
        }
    }
}
