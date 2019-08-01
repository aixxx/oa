<?php
namespace App\UserAccount;


use App\Models\Financial;
use App\Models\TransactionLog;
use Exception;
use Illuminate\Support\Facades\DB;

class AccountLog
{
    /*public $tables = [
        "user_id" => '用户id',
        "department_id" => '部门id',
        "company_id" => '公司id',
        'source'=>'来源，1 五个流',
        "outer_id" => '记录外部关联id',
        "is_rpc"=> '是否通过rpc传输',
        "model_name"=> '模型，get_class(models名称)',
        "amount"=> '金额，单位分',
        "category"=> '类型：1->报销, 2->借款, 3->还款, 4->收款，5->支付',
        "type"=> '交易类型, 1=>对内交易（收）, 2=>对内交易（支）, 3 => 对外交易（收）, 4=>对外交易（支），5=>分红支出，6=>资产',
        "is_bill"=> '是否有单据',
        "is_jysr"=> '是否是经营收入',
        "in_out"=> '1=>应收, 2=>应付',
        "is_more_department"=> '是否是多部门分摊',
        "status"=> '审核状态：1=>审核通过, 2=>财务付款完成',
        "status_end_time"=> '审核时间',
    ];*/
    public $tables1 = [
        "user_id" => '必填用户id',
        "department_id" => '必填部门id',
        "company_id" => '公司id',
        "outer_id" => '必填记录外部关联id',
        "model_name"=> '必填模型，app/models/文件名 get_class(new Financial());',
        "amount"=> '必填金额，单位分',
        "category"=> '必填类型：1->报销, 2->借款, 3->还款, 4->收款，5->支付',
        "type"=> '必填交易类型, 1=>对内交易（收）, 2=>对内交易（支）, 3 => 对外交易（收）, 4=>对外交易（支），5=>分红支出，6=>资产',
        "is_bill"=> '必填是否有单据',
        "status_start_time"=> '订单创建时间',
        "is_more_department"=> '必填是否是多部门分摊',
    ];
    // 应收
    private $ins=[3,4];
    private $cates = [
        1=>'报销', 2=>'借款', 3=>'还款', 4=>"收款",5=>"支付"
    ];
    // 个人
    public static function PerInsert($data = []) {
        $model = new static();
        foreach ($model->tables1 as $key=>$table) {
            if(!isset($data[$key])) {
                throw new Exception(sprintf('%s:%s', $key, $table));
            }
        }

        $map['user_id'] = $data['user_id'];
        $map['department_id'] = $data['department_id'];
        $map['outer_id'] = $data['outer_id'];
        $map['source'] = 1;

        $t = TransactionLog::where($map)->first();

        if($t) {
            throw new Exception(sprintf('logs已存在'));
        }

        $data['title'] = $model->cates[$data['category']];
        $data['is_rpc'] = 0;
        // 判断是否是应收
        if(in_array($data['type'], $model->ins)) {
            $data['in_out'] = 1;
        }else {
            $data['in_out'] = 2;
        }
        $data['status'] = 1;
        $data['is_jysr'] = 0;
        return TransactionLog::create($data);
    }
    // 更新id
    public static function Update($outer_id,$qishu = '', $status = '') {
        if($status == 8) {
            $transactionLog = TransactionLog::where('outer_id', $outer_id)->first();
            $transactionLog->qishu = $qishu;
            $transactionLog->status_end_time = date('Y-m-d H:i:s');
            $transactionLog->save();
        }
    }
}
