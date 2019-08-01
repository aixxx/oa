<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersDimissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_dimission', function(Blueprint $table)
		{
			$table->increments('id')->comment('内部系统uid');
			$table->integer('user_id')->comment('员工ID');
			$table->boolean('is_voluntary')->default(1)->comment('是否主动离职(1:是;2:否)');
			$table->boolean('is_sign')->nullable()->default(1)->comment('直属领导是否已签字(1:已签;2:未签)');
			$table->boolean('is_complete')->nullable()->default(1)->comment('流程手续是否已走完(1:是;2:否)');
			$table->text('reason', 65535)->nullable()->comment('原因');
			$table->string('note')->nullable()->comment('备注信息');
			$table->text('interview_result', 65535)->nullable()->comment('面谈结论');
			$table->boolean('status')->default(1)->comment('状态1.有效；2.删除');
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
		Schema::drop('users_dimission');
	}

}
