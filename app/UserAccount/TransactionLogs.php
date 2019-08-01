<?php
namespace App\UserAccount;


use App\Models\Financial;
use App\Models\TransactionLog;

class TransactionLogs
{
    public $tables = [
        "user_id" => '用户id',
        "department_id" => '部门id',
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
    ];
    public $tables1 = [
        "user_id" => '用户id',
        "department_id" => '部门id',
        "outer_id" => '记录外部关联id',
        "model_name"=> '模型，app/models/文件名 get_class(new Financial());',
        "amount"=> '金额，单位分',
        "category"=> '类型：1->报销, 2->借款, 3->还款, 4->收款，5->支付',
        "type"=> '交易类型, 1=>对内交易（收）, 2=>对内交易（支）, 3 => 对外交易（收）, 4=>对外交易（支），5=>分红支出，6=>资产',
        "is_bill"=> '是否有单据',
        "is_jysr"=> '是否是经营收入',
        "is_more_department"=> '是否是多部门分摊',
    ];
    // 应收
    private $ins=[3,4];
    private $cates = [
        1=>'报销', 2=>'借款', 3=>'还款', 4=>"收款",5=>"支付"
    ];
    // 插入wugeliu
    public function insert($data = []) {

        foreach ($this->tables1 as $key=>$table) {
            if(!isset($data[$key])) {
                return "{$key} ：必填->". $table;
            }
        }

        $data['title'] = $this->cates[$data['category']];
        $data['is_rpc'] = 0;
        $data['amount'] = $data['amount'] * 100;
        // 判断是否是应收
        if(in_array($data['category'], $this->ins)) {
            $data['in_out'] = 1;
        }else {
            $data['in_out'] = 2;
        }
        $data['status'] = 1;
        TransactionLog::create($data);
    }
    // 更新id
    public function Update($outer_id, $model) {
        $transactionLog = TransactionLog::where('model_name', $model)->findOrFail($outer_id);
        $transactionLog->status = 2;
        $transactionLog->save();
    }
}
