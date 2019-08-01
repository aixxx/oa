<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSpecificsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_specifics` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '规格id',
  `name` varchar(55) DEFAULT NULL COMMENT '规格名称',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `search_index` tinyint(1) DEFAULT '0' COMMENT '是否需要检索',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='规格表';
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
        Schema::dropIfExists('pas_specifics');
    }
}
