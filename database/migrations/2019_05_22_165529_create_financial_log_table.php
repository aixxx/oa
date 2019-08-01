<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancialLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `financial_log` (
  `id` bigint(20) NOT NULL COMMENT '财务统计日志',
  `financial_id` int(10) DEFAULT NULL COMMENT '财务id',
  `operator` int(10) DEFAULT NULL COMMENT '操作人',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态：1：启用',
  `remarks` varchar(255) DEFAULT NULL COMMENT '操作备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='财务操作日志';
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
        Schema::dropIfExists('financial_log');
    }
}
