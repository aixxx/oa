<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowFlowTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_flow_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type_name', 32);
			$table->integer('sortby')->nullable()->default(1000)->comment('排序');
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
		Schema::drop('workflow_flow_types');
	}

}
