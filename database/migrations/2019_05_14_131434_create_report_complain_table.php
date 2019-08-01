<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportComplainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_complain', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->length(11)->comment('员工id');
            $table->string('title',50)->nullable()->comment('标题');
            $table->text('content')->nullable()->comment('内容');
            $table->string('img_url',255)->nullable()->comment('图片');
            $table->string('video_url',255)->nullable()->comment('视频');
            $table->string('file_url',255)->nullable()->comment('文件');
            $table->string('audio_url',255)->nullable()->comment('音频');
            $table->char("type",1)->nullable()->comment('数据类型 1投诉 2举报');
            $table->char("state",2)->default(-1)->comment('状态  -1待处理  1 已处理 ');
            $table->integer("entry_id")->nullable()->length(11)->comment('申请单id');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `report_complain` comment '举报投诉记录表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_complain');
    }
}
