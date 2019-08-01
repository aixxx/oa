<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformanceTemplateSonTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('performance_template_son', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('pt_id')->unsigned()->nullable()->comment('绩效模板编号');
			$table->string('title', 100)->nullable()->comment('指标维度名称');
			$table->integer('numb')->nullable()->default(0)->comment('总权重');
			$table->bigInteger('approval_id')->unsigned()->nullable()->comment('考核人id');
			$table->boolean('status')->nullable()->default(1)->comment('状态');
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
		Schema::drop('performance_template_son');
	}

}
