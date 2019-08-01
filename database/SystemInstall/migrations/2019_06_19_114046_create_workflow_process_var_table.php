<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowProcessVarTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_process_var', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('process_id');
			$table->integer('flow_id');
			$table->string('expression_field', 45);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_process_var');
	}

}
