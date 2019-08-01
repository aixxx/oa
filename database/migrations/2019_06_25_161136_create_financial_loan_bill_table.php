<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancialLoanBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = <<<EOF
CREATE TABLE `financial_loan_bill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `financial_id` int(10) DEFAULT NULL COMMENT '关联财务id',
  `loan_bill_id` int(10) DEFAULT NULL COMMENT '借款单id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='关联多个借款单';
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
        Schema::dropIfExists('financial_loan_bill');
    }
}
