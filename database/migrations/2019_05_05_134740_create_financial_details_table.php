<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFinancialDetailsTable.
 */
class CreateFinancialDetailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 $sql = <<<EOF
CREATE TABLE `financial_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `financial_id` int(10) DEFAULT NULL,
  `money` bigint(10) DEFAULT '0' COMMENT '金额',
  `projects_id` int(10) DEFAULT NULL COMMENT '项目id',
  `projects_condition` varchar(255) DEFAULT NULL COMMENT '项目条件',
  `reason` varchar(255) DEFAULT NULL COMMENT '报销事由',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='财务明细表';
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
		Schema::drop('financial_details');
	}
}
