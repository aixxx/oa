<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSpecificItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_specific_items', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('规格项id');
			$table->integer('spec_id')->nullable()->index('spec_id')->comment('规格id');
			$table->string('name', 54)->nullable()->index('item')->comment('规格项名称');
			$table->integer('sort')->nullable()->comment('排序');
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
		Schema::drop('pas_specific_items');
	}

}
