<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronPushRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_cron_push_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('push_at')->notnull()->defaul(0)->comment('推送执行时间');
            $table->integer('type')->notnull()->comment('目标类型： 1、任务 2、日程');
            $table->integer('type_pid')->notnull()->comment('目标类型的主键ID');
            $table->string('type_title', 20)->notnull()->comment('目标类型描述 任务/日程');
            $table->string('content', 240)->notnull()->comment('推送内容');
            $table->integer('channel')->nullable()->default(1)->comment('推送频道');
            $table->integer('notice_type')->nullable()->default(1)->comment('推送渠道： 1、站内 2、手机');
            $table->integer('times')->nullable()->default(1)->comment('推送频率');
            $table->integer('push_times')->nullable()->default(1)->comment('已推送次数');
            $table->integer('is_expire')->nullable()->default(0)->comment('是否失效 1、失效 0、有效');
            $table->string('target_uids', 255)->notnull()->comment('推送对象');
            $table->text('remark')->nullable()->comment('备注');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['type', 'type_pid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_cron_push_records');
    }
}
