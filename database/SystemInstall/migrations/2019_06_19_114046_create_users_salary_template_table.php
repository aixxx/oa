<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersSalaryTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_salary_template', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('template_name')->nullable()->comment('模板名称');
			$table->bigInteger('create_salary_user_id')->unsigned()->nullable()->comment('创建者编号');
			$table->string('create_salary_user_name', 100)->nullable()->comment('创建者名称');
			$table->integer('company_id')->unsigned()->nullable()->comment('公司编号');
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
		Schema::drop('users_salary_template');
	}

}
