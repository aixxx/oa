<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanySealsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_seals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('seals_type_id')->comment('印章类型id');
			$table->string('seal_img', 191)->comment('印章图片url');
			$table->integer('upload_user_id')->comment('上传者的用户id');
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
		Schema::drop('company_seals');
	}

}
