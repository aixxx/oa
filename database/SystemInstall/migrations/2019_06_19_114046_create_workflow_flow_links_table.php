<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowFlowLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_flow_links', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('flow_id');
			$table->string('type', 45);
			$table->integer('process_id');
			$table->integer('next_process_id');
			$table->string('auditor', 512);
			$table->string('depands', 191)->comment('依赖');
			$table->string('expression', 2048)->comment('转出条件对应的表达式');
			$table->integer('sort');
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
		Schema::drop('workflow_flow_links');
	}

}
