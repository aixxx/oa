<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowEntryDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_entry_data', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id');
			$table->integer('flow_id');
			$table->string('field_name', 64);
			$table->text('field_value', 65535);
			$table->string('field_remark');
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
		Schema::drop('workflow_entry_data');
	}

}
