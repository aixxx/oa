<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasLogisticsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_logistics', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('status')->nullable()->default(0)->comment('状态');
			$table->string('title', 100)->nullable()->comment('物流名');
			$table->softDeletes();
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
		Schema::drop('pas_logistics');
	}

}
