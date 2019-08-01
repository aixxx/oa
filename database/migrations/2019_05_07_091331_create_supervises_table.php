<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupervisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `supervises` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '指派者id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '督办用户id',
  `relate_id` int(11) NOT NULL COMMENT '关联表主键id',
  `type` tinyint(4) DEFAULT '0' COMMENT '类型 1任务',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='督办表';
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
        Schema::dropIfExists('supervises');
    }
}
