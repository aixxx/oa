<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportComplainTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report_complain', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable()->comment('员工id');
			$table->string('title', 50)->nullable()->comment('标题');
			$table->text('content', 65535)->nullable()->comment('内容');
			$table->string('img_url')->nullable()->comment('图片');
			$table->string('video_url')->nullable()->comment('视频');
			$table->string('file_url')->nullable()->comment('文件');
			$table->string('audio_url')->nullable()->comment('音频');
			$table->char('type', 1)->nullable()->comment('数据类型 1投诉 2举报');
			$table->char('state', 2)->default(-1)->comment('状态  -1待处理  1 已处理 ');
			$table->integer('entry_id')->nullable()->comment('申请单id');
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
		Schema::drop('report_complain');
	}

}
