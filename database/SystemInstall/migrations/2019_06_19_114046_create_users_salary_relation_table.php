<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersSalaryRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_salary_relation', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('template_id')->unsigned()->nullable()->index('users_salary_relation_template_id_foreign')->comment('模板id');
			$table->integer('field_id')->unsigned()->nullable()->comment('字典字段id');
			$table->boolean('status')->nullable()->comment('类型：1，薪资 2，补贴 3，奖金');
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
		Schema::drop('users_salary_relation');
	}

}
