<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFinancialPicsTable.
 */
class CreateFinancialPicsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 $sql = <<<EOF
CREATE TABLE `financial_pic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `financial_id` int(10) unsigned DEFAULT NULL,
  `pic_type` tinyint(3) DEFAULT NULL COMMENT '图片类型:1：图片 2：文件 3：发票',
  `pic_url` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COMMENT='财务图片表';
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
		Schema::drop('financial_pics');
	}
}
