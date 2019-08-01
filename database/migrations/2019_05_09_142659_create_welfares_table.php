<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateWelfaresTable.
 */
class CreateWelfaresTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$sql = <<<EOF
CREATE TABLE `welfare` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entries_id` int(10) DEFAULT NULL COMMENT '关联workflow_entries的id',
  `title` varchar(100) DEFAULT NULL COMMENT '福利标题',
  `promoter` int(10) DEFAULT NULL COMMENT '发起人',
  `content` varchar(255) DEFAULT NULL COMMENT '福利内容',
  `condition_methods` varchar(200) DEFAULT NULL COMMENT '福利领取条件及方式',
  `issuer` int(10) DEFAULT NULL COMMENT '发放人',
  `startdate` datetime DEFAULT NULL COMMENT '开始日期',
  `enddate` datetime DEFAULT NULL COMMENT '结束日期',
  `status` tinyint(2) DEFAULT NULL COMMENT '审批状态：1:审核中 2：审核通过 3：已删除 4：待删除',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='福利';
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
		Schema::drop('welfares');
	}
}
