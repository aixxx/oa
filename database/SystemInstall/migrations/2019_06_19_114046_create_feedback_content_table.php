<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedbackContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedback_content', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->boolean('tid')->nullable()->comment('反馈类型id');
			$table->string('title')->nullable()->comment('标题');
			$table->string('content')->nullable()->comment('内容');
			$table->boolean('way')->nullable()->comment('2：匿名反馈，3：实名反馈');
			$table->dateTime('publish_time')->comment('发布时间');
			$table->integer('status')->nullable()->default(2)->comment('2：未回复，3：已回复未读，4：回复已读');
			$table->bigInteger('uid')->nullable()->comment('发布人员id');
			$table->timestamps();
			$table->string('image')->nullable()->comment('图片附件');
			$table->string('video')->nullable()->comment('视频附件');
			$table->string('audio')->nullable()->comment('音频附件');
			$table->string('other_file')->nullable()->comment('文件附件');
			$table->boolean('relation_type')->comment('关联类型 1-评分');
			$table->integer('relation_id')->comment('关联id');
			$table->softDeletes()->index()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feedback_content');
	}

}
