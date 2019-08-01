<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_brands` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '品牌表',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `logo` varchar(80) NOT NULL DEFAULT '' COMMENT '品牌logo',
  `desc` text NOT NULL COMMENT '品牌描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '品牌地址',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `status` tinyint(1) DEFAULT '0' COMMENT '0不使用 1使用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=382 DEFAULT CHARSET=utf8 COMMENT='商品品牌表';
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
        Schema::dropIfExists('pas_brands');
    }
}
