<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancialCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `financial_customer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_type` varchar(100) DEFAULT '1' COMMENT '类型:1 客户 2 供应商 3内部单位',
  `financial_id` int(10) DEFAULT NULL COMMENT '财务id',
  `title` varchar(100) DEFAULT NULL COMMENT '名称',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='财务 -网络单位相关';
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
        Schema::dropIfExists('financial_customer');
    }
}
