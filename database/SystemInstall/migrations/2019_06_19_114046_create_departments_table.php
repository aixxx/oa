<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('departments', function(Blueprint $table)
		{
			$table->increments('auto_id');
			$table->integer('id')->default(0)->index()->comment('部门Id');
			$table->string('name', 191);
			$table->integer('parent_id');
			$table->integer('order');
			$table->boolean('deepth')->nullable();
			$table->timestamps();
			$table->boolean('is_sync_wechat')->default(1)->comment('是否要同步企业微信');
			$table->softDeletes();
			$table->string('tel', 100)->nullable()->comment('公司电话');
			$table->integer('attendance_id')->default(1)->comment('考勤组ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('departments');
	}

}
