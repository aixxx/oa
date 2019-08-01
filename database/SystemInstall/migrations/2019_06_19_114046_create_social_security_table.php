<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSocialSecurityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_security', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->comment('公司编号');
			$table->string('name', 100)->comment('社保名称');
			$table->string('english_name', 100)->comment('英文名称');
			$table->string('company_proportion', 100)->comment('交金比例');
			$table->integer('create_user_id')->comment('创建人编号');
			$table->string('create_user_name', 100)->comment('创建人名称');
			$table->timestamps();
			$table->softDeletes()->index();
			$table->string('personal_proportion', 100)->comment('个人缴纳比例');
			$table->string('payment_standard', 100)->comment('基本工资标准');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_security');
	}

}
