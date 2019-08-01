<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformanceTemplateQuotaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('performance_template_quota', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->string('title', 100)->nullable()->comment('指标名称');
			$table->string('standard', 100)->nullable()->comment('考核标准');
			$table->integer('weight')->nullable()->comment('权重');
			$table->string('value', 50)->nullable()->comment('目标值');
			$table->timestamps();
			$table->bigInteger('pts_id')->nullable()->default(0)->comment('绩效模板维度id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('performance_template_quota');
	}

}
