<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanySealsTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_seals_type', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('公司id');
			$table->string('seal_type_name', 191)->comment('印章类型名称');
			$table->integer('create_user_id')->comment('创建者的用户id');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_seals_type');
	}

}
