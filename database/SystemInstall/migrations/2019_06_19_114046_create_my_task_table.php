<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMyTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('my_task', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('tid')->index()->comment('任务id');
			$table->bigInteger('uid')->comment('用户id');
			$table->integer('pid')->comment('部门项目id');
			$table->integer('parent_id')->nullable()->default(0)->comment('父级任务id，此表');
			$table->boolean('level')->nullable()->default(0)->comment('任务级别');
			$table->string('parent_ids', 300)->nullable()->default('0')->comment('所有父级');
			$table->integer('temp_id')->nullable()->default(0)->comment('分组id');
			$table->string('type_name', 191)->nullable()->comment('类型名称');
			$table->integer('status')->nullable()->default(0)->comment('任务状态（-1拒绝,0默认,1待确认,2待办理,3待评价,4完成）');
			$table->boolean('user_type')->nullable()->comment('用户类型（1接收人，2抄送人，3部门下的项目）');
			$table->integer('create_user_id')->comment('创建者id');
			$table->dateTime('accept_time')->nullable();
			$table->dateTime('finish_time')->default('0000-00-00 00:00:00')->comment('完成时间');
			$table->dateTime('start_time')->default('0000-00-00 00:00:00')->comment('任务开始时间');
			$table->dateTime('end_time')->default('0000-00-00 00:00:00')->comment('任务结束时间');
			$table->dateTime('comment_time')->default('0000-00-00 00:00:00')->comment('评论时间');
			$table->softDeletes();
			$table->timestamps();
			$table->text('content', 65535)->nullable()->comment('任务内容');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('my_task');
	}

}
