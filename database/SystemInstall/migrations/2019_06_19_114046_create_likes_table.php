<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLikesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('likes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->default(0)->comment('点赞用户id');
			$table->integer('relate_id')->comment('关联表主键id');
			$table->boolean('type')->nullable()->default(0)->comment('点赞类型 1汇报');
			$table->timestamps();
			$table->dateTime('deleted_at')->default('0000-00-00 00:00:00')->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('likes');
	}

}
