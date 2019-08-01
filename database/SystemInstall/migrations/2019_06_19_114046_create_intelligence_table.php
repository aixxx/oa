<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIntelligenceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('intelligence', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('class_id')->nullable()->comment('情报分类id');
			$table->string('title', 50)->nullable()->comment('目标名称');
			$table->integer('user_id')->nullable()->comment('情报员id');
			$table->text('demand', 65535)->nullable()->comment('情报需求');
			$table->text('targetData', 65535)->nullable()->comment('目标资料');
			$table->string('img_url')->nullable()->comment('图片');
			$table->string('video_url')->nullable()->comment('视频');
			$table->string('file_url')->nullable()->comment('文件');
			$table->string('audio_url')->nullable()->comment('音频');
			$table->dateTime('startTime')->nullable()->comment('工作周期开始时间');
			$table->dateTime('endTime')->nullable()->comment('工作周期结束时间');
			$table->string('cost', 191)->nullable()->comment('实施预计金额');
			$table->char('state', 2)->default(-1)->comment('状态 -1 草稿  1 发布 2 已指派 3待审批  4已完成 ');
			$table->char('classified', 1)->nullable()->comment('秘密等级  1公开 2私密 3绝密 4机密');
			$table->string('participation', 191)->comment('可参与人数');
			$table->string('userNum', 191)->comment('认领人数');
			$table->string('endNum', 191)->comment('最终确认人数');
			$table->string('auditNum', 191)->comment('审核完成人数');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('intelligence');
	}

}
