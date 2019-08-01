<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasCommodityCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_commodity_category', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->integer('pid')->nullable()->default(0)->comment('上级id');
			$table->string('title', 100)->nullable()->comment('分类名称');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0已取消 1可使用');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_commodity_category');
	}

}
