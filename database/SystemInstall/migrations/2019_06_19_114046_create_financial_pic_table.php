<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinancialPicTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('financial_pic', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('financial_id')->unsigned()->nullable();
			$table->boolean('pic_type')->nullable()->comment('图片类型:1：图片 2：文件 3：发票');
			$table->string('pic_url', 200)->nullable();
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
		Schema::drop('financial_pic');
	}

}
