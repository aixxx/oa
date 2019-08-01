<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkReportRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_report_rules', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->comment = '汇报规则表';
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->comment('创建者id');
            $table->unsignedTinyInteger('report_type')->default(0)->comment('0-日报 1-周报 2-月报');
            $table->string('sender',255)->comment('发送者id，逗号隔开');
            $table->unsignedTinyInteger('send_cycle')->default(0)->comment('0每天1每周2每月');;
            $table->string('send_date',50)->comment('提交日期（按天）');
            $table->string('start_time',50)->comment('开始时间');
            $table->string('end_time',50)->comment('截止时间');
            $table->unsignedTinyInteger('is_legal_day_send')->default(0)->comment('0法定假日不提交1提交');
            $table->string('groups_id',255)->nullable()->comment('统计结果发送群id，逗号隔开');
            $table->unsignedTinyInteger('is_remind')->default(0)->comment('0不提醒1提醒');
            $table->string('remind_content',255)->nullable()->comment('提醒内容');
            $table->unsignedTinyInteger('is_delete')->default(0)->comment('0正常1删除');
            $table->unsignedInteger('created_at')->default(0)->comment('创建时间');
            $table->unsignedInteger('updated_at')->default(0)->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('work_report_rules');
    }
}
