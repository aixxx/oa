<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedbackAccssoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedback_accssory', function(Blueprint $table)
		{
			$table->increments('id')->comment('模板编号');
			$table->boolean('status')->comment('1：评论，2：反馈');
			$table->bigInteger('rid')->comment('关联id');
			$table->string('name')->comment('附件名称');
			$table->string('type')->comment('附件类型');
			$table->integer('size')->comment('附件大小');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feedback_accssory');
	}

}
