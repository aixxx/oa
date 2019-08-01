<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersSalaryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_salary', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('template_id')->unsigned()->nullable()->comment('模板编号');
			$table->integer('relation_id')->unsigned()->nullable()->index('users_salary_relation_id_foreign')->comment('关联编号');
			$table->integer('field_id')->unsigned()->nullable()->comment('字段编号');
			$table->boolean('status')->nullable()->comment('类型：1，薪资 2，补贴 3，奖金');
			$table->integer('company_id')->unsigned()->nullable()->comment('公司编号');
			$table->string('field_name', 200)->nullable()->comment('字段名称');
			$table->string('field_data', 200)->nullable()->comment('字段数据');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('用户编号');
			$table->bigInteger('contract_id')->unsigned()->nullable()->comment('模板id');
			$table->bigInteger('create_salary_user_id')->unsigned()->nullable()->comment('创建者编号');
			$table->string('create_salary_user_name', 100)->nullable()->comment('创建者名称');
			$table->integer('version')->unsigned()->index('users_salary_version_foreign')->comment('薪资版本');
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
		Schema::drop('users_salary');
	}

}
