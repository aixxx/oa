<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntelligenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intelligence', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('class_id')->nullable()->length(11)->comment('情报分类id');
            $table->string('title',50)->nullable()->comment('目标名称');
            $table->integer('user_id')->nullable()->length(11)->comment('情报员id');
            $table->text('demand')->nullable()->comment('情报需求');
            $table->text('targetData')->nullable()->comment('目标资料');
            $table->string('img_url',255)->nullable()->comment('图片');
            $table->string('video_url',255)->nullable()->comment('视频');
            $table->string('file_url',255)->nullable()->comment('文件');
            $table->string('audio_url',255)->nullable()->comment('音频');
            $table->timestamp('startTime')->nullable()->comment('工作周期开始时间');
            $table->timestamp('endTime')->nullable()->comment('工作周期结束时间');
            $table->string("cost")->nullable()->comment('实施预计金额');
            $table->char("state",2)->default(-1)->comment('状态 -1 草稿  1 发布 2 已指派 3待审批  4已完成 ');
            $table->char("classified",1)->nullable()->comment('秘密等级  1公开 2私密 3绝密 4机密');
            $table->string("participation")->comment('可参与人数');
            $table->string("userNum")->comment('认领人数');
            $table->string("endNum")->comment('最终确认人数');
            $table->string("auditNum")->comment('审核完成人数');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `intelligence` comment '情报表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intelligence');
    }
}
