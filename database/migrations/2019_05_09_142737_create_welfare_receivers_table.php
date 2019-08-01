<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateWelfareReceiversTable.
 */
class CreateWelfareReceiversTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$sql = <<<EOF
CREATE TABLE `welfare_receiver` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `welfare_id` int(10) DEFAULT NULL COMMENT '福利id',
  `minister` int(10) DEFAULT NULL COMMENT '主管id',
  `user_id` int(10) DEFAULT NULL COMMENT '领取人id',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态：1 申请中 2 申请通过 3 申请拒绝',
  `is_draw` tinyint(2) DEFAULT NULL COMMENT '是否领取: 1 未领取  2 已领取',
  `reason` varchar(100) DEFAULT NULL COMMENT '说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `welfare_id` (`welfare_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='福利领取人';
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
		Schema::drop('welfare_receivers');
	}
}
