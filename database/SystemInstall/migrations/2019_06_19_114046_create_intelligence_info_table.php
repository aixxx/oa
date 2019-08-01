<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIntelligenceInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('intelligence_info', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('inte_content', 65535)->nullable()->comment('情报内容');
			$table->text('inte_demand', 65535)->nullable()->comment('附件需求');
			$table->string('file_upload', 191)->nullable()->comment('附件');
			$table->dateTime('time')->nullable()->comment('时间');
			$table->string('bank', 191)->nullable()->comment('开户行');
			$table->string('card_num', 191)->nullable()->comment('银行账号');
			$table->char('auditstate', 1)->comment('状态 1 审核中 2已完成');
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
		Schema::drop('intelligence_info');
	}

}
