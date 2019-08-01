<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoteOptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vote_option', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('v_id')->default(0)->index()->comment('投票编号');
			$table->string('option_name', 100)->nullable()->comment('投票选项名称');
			$table->boolean('state')->nullable()->default(1)->comment('选项状态 1，未通过 2，已通过');
			$table->integer('percentage')->default(0)->comment('1');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('vote_number')->comment('选项总票数');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vote_option');
	}

}
