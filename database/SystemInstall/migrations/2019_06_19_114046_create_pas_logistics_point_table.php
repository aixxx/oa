<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasLogisticsPointTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_logistics_point', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('status')->nullable()->default(0)->comment('状态');
			$table->string('point')->nullable()->default('0')->comment('网点');
			$table->string('tel', 100)->nullable()->default('0')->comment('电话');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('logistics_id')->nullable()->comment('物流ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_logistics_point');
	}

}
