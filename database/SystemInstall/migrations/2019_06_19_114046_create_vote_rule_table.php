<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoteRuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vote_rule', function(Blueprint $table)
		{
			$table->bigInteger('id')->comment('自动编号');
			$table->string('rule_name', 100)->nullable()->comment('规则名称');
			$table->boolean('is_show')->nullable()->comment('是否隐藏 1，否 2，是');
			$table->integer('passing_rate')->nullable()->comment('投票通过率');
			$table->integer('vote_number')->nullable()->comment('投票票数');
			$table->integer('job_grade')->nullable()->comment('职级编号');
			$table->timestamps();
			$table->softDeletes()->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vote_rule');
	}

}
