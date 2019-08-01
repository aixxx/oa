<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoteParticipantTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vote_participant', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('v_id')->comment('投票编号');
			$table->integer('create_vote_user_id')->nullable()->comment('创建人编号');
			$table->string('create_vote_user_name', 100)->nullable()->comment('创建人名称');
			$table->integer('user_id')->nullable()->comment('参与人编号');
			$table->string('user_name', 100)->nullable()->comment('参与人名称');
			$table->text('describe', 65535)->nullable()->comment('投票描述');
			$table->integer('confirm_yes')->nullable()->default(0)->comment('状态：0未投票，1已投票');
			$table->timestamps();
			$table->softDeletes()->index('vote_deleted_at_index');
			$table->string('avatar')->nullable()->comment('用户头像');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vote_participant');
	}

}
