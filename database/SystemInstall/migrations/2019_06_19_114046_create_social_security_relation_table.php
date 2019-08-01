<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSocialSecurityRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_security_relation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ss_id')->default(0)->comment('社保配置ID');
			$table->integer('create_user_id')->default(0)->comment('创建者ID');
			$table->string('create_user_name', 100)->comment('创建者名称');
			$table->integer('user_id')->comment('用户ID');
			$table->string('user_name', 11)->comment('用户名称');
			$table->integer('company_id')->comment('公司ID');
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
		Schema::drop('social_security_relation');
	}

}
