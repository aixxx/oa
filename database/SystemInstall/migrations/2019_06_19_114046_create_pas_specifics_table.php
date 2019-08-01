<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSpecificsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_specifics', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('规格id');
			$table->string('name', 55)->nullable()->comment('规格名称');
			$table->integer('sort')->nullable()->default(0)->comment('排序');
			$table->boolean('search_index')->nullable()->default(0)->comment('是否需要检索');
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
		Schema::drop('pas_specifics');
	}

}
