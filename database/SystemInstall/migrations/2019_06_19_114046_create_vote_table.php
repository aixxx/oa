<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vote', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->string('vote_title')->nullable()->comment('投票标题');
			$table->boolean('vote_type_id')->nullable()->comment('投票类型');
			$table->char('vote_type_name', 191)->nullable()->comment('类型名称');
			$table->text('describe', 65535)->nullable()->comment('投票描述');
			$table->string('enclosure_url', 191)->nullable()->comment('投票附件');
			$table->dateTime('end_at')->nullable()->comment('投票结束时间');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->integer('create_vote_user_id')->nullable()->index()->comment('用户编号');
			$table->boolean('prompt_type')->nullable()->comment('提醒方式');
			$table->integer('rule_id')->nullable()->comment('投票规则编号');
			$table->integer('company_id')->nullable()->comment('公司编号');
			$table->integer('department_id')->nullable()->comment('部门编号');
			$table->integer('passing_rate')->nullable()->comment('投票通过率');
			$table->string('user_name', 100)->nullable()->comment('用户名称');
			$table->boolean('selection_type')->nullable()->comment('选项类型 1：单选，2：多选');
			$table->boolean('state')->nullable()->default(1)->comment('状态 1：正常，2：已取消，3：已通过，4：无效');
			$table->integer('number')->nullable()->comment('投票票数');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vote');
	}

}
