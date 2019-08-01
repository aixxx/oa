<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancialChilderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          $sql = <<<EOF
CREATE TABLE `financial_childer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `financial_id` bigint(20) DEFAULT NULL COMMENT '我的财务id',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `voucher_id` int(10) DEFAULT NULL,
  `money` decimal(13,2) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `financial_deleted_index` (`financial_id`,`deleted_at`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='财务子单信息';
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
        Schema::dropIfExists('financial_childer');
    }
}
