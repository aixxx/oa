<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIntelligenceUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('intelligence_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable()->comment('情报员id');
			$table->integer('inte_id')->nullable()->comment('情报目标id');
			$table->char('state', 2)->default(-1)->comment('状态 -1无 1同意 2 拒绝');
			$table->char('attribute', 1)->comment('属性 1认领 2指派');
			$table->text('reason', 65535)->comment('理由');
			$table->text('inte_content', 65535)->nullable()->comment('情报内容');
			$table->text('inte_demand', 65535)->nullable()->comment('附件需求');
			$table->string('file_upload', 191)->nullable()->comment('附件');
			$table->dateTime('time')->nullable()->comment('时间');
			$table->string('bank', 191)->nullable()->comment('开户行');
			$table->string('card_num', 191)->nullable()->comment('银行账号');
			$table->char('auditstate', 1)->nullable()->comment('状态 1 审核中 2已完成');
			$table->integer('entry_id')->nullable()->comment('申请单id');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['user_id','inte_id'], 'user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('intelligence_users');
	}

}
