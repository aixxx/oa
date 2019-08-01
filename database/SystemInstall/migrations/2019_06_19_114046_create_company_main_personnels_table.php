<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyMainPersonnelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_main_personnels', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('关联企业');
			$table->string('name', 32)->nullable()->default('')->comment('姓名');
			$table->string('position', 32)->nullable()->default('')->comment('职位(董事/监事/经理等)');
			$table->timestamps();
			$table->boolean('status')->default(1)->comment('状态1.有效；2.删除');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_main_personnels');
	}

}
