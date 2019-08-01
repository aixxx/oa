<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF


CREATE TABLE `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户',
  `entry_id` int(11) NOT NULL COMMENT '文件公文流id',
  `doc_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '公文标题',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态 默认 0:审批中  1:审批过',
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '公文字号',
  `primary_dept` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所属部门',
  `primary_dept_id` int(11) DEFAULT NULL COMMENT '所属部门id',
  `doc_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类型: 1:通知 2:公告 3:通报 4:议案 5:报告 6:请示 7:批复 8:意见 9:函 10:会议纪要',
  `secret_level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '秘密等级: 1:公开 2:秘密 3:机密 4:绝密',
  `urgency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '紧急程度: 1:普通 2:加急 3:特级',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '主题词',
  `main_dept` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '主送部门',
  `main_dept_id` int(11) DEFAULT NULL COMMENT '主送部门id',
  `copy_dept` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '抄送部门',
  `copy_dept_id` int(11) DEFAULT NULL COMMENT '抄送部门id',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '文件内容',
  `file_upload` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '上传的文件',
  `authorized_userId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所有签批人id',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文件公文表';
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
        Schema::dropIfExists('document');
    }
}
