<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTotalCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('total_comment', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('type')->nullable()->comment('类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10汇报,11工作流)');
			$table->integer('relation_id')->comment('关联id');
			$table->integer('uid')->comment('用户id');
			$table->string('comment_text')->comment('文字评论');
			$table->text('comment_img', 65535)->nullable()->comment('图片评论');
			$table->text('comment_field', 65535)->nullable()->comment('图片附件');
			$table->timestamp('comment_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('评价时间');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('entry_id')->nullable()->comment('申请编号');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('total_comment');
	}

}
