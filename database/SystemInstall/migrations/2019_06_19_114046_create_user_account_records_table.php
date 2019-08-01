<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAccountRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_account_records', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('用户id');
			$table->string('title', 191)->comment('消费项目标题');
			$table->string('sub', 191)->comment('副标题');
			$table->boolean('is_correlation_model')->comment('是否关联模型');
			$table->integer('model_id')->comment('模型ID');
			$table->string('model_name', 50)->comment('模型名称');
			$table->integer('account_type_id')->comment('收益类型 1:投资 2:工资 3:分红 -1:支出');
			$table->integer('balance')->comment('收益金额 单位分');
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
		Schema::drop('user_account_records');
	}

}
