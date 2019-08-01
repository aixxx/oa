<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `financial` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `code` varchar(100) NOT NULL COMMENT '财务编号',
  `flow_id` int(10) NOT NULL COMMENT '流程编号',
  `title` varchar(100) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL COMMENT '申请人id',
  `applicant_chinese_name` varchar(50) DEFAULT NULL COMMENT '中文名称',
  `company_id` int(10) DEFAULT NULL,
  `primary_dept` varchar(100) DEFAULT NULL COMMENT '主部门',
  `entry_id` int(10) DEFAULT NULL COMMENT '申请流程id',
  `status` tinyint(2) DEFAULT '1' COMMENT '当前状态 -1:审批拒绝 1：待审批 2：批复中 3：审批完成 4：待入账 5：待收支 6：已收支 7：待发票 8：已完成',
  `budget_id` int(10) DEFAULT NULL COMMENT '预算单id',
  `expense_amount` bigint(20) DEFAULT NULL,
  `account_type` varchar(20) DEFAULT NULL COMMENT '账号类型 :支付宝  微信',
  `account_number` varchar(60) DEFAULT NULL COMMENT '账户账号',
  `account_period` int(10) DEFAULT NULL COMMENT '账期',
  `unittype` varchar(20) DEFAULT NULL COMMENT '往来单位类型',
  `current_unit` varchar(100) DEFAULT NULL COMMENT '往来单位',
  `transaction` tinyint(2) DEFAULT '1' COMMENT '内外交易:1:对内交易 2：对外交易',
  `fee_booth` tinyint(2) DEFAULT '2' COMMENT '费用公摊:1:是 2：否',
  `loan_bill` tinyint(2) DEFAULT '2' COMMENT '借款单:1:是 2：否',
  `associated_projects` tinyint(2) DEFAULT '2' COMMENT '关联项目:1:是 2：否',
  `linked_order` tinyint(2) DEFAULT '2' COMMENT '关联订单:1:是 2：否',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'timestamp',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `type_id` tinyint(4) DEFAULT NULL COMMENT '0:审批单\r\n1:待审批',
  `procs_id` int(10) DEFAULT NULL COMMENT '审批人id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COMMENT='财务流表';
EOF;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial');
    }
}
