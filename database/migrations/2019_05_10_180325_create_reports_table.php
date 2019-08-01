<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `reports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `template_id` int(11) NOT NULL COMMENT '模板id',
  `company_id` int(11) NOT NULL COMMENT '企业id',
  `content` text NOT NULL COMMENT '汇报模版自定义内容',
  `img` varchar(255) DEFAULT NULL COMMENT '图片',
  `accessory` varchar(255) DEFAULT NULL COMMENT '附件',
  `select_depart` text NOT NULL COMMENT '选中的部门',
  `select_user` text NOT NULL COMMENT '接收汇报者',
  `read` text COMMENT '已读人员',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '删除时间',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `template_id` (`template_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;
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
        Schema::dropIfExists('reports');
    }
}
