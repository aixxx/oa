<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformanceApplicationSonTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('performance_application_son', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('auditor_id')->nullable()->default(0)->comment('审核人id');
			$table->bigInteger('pa_id')->nullable()->comment('绩效申请记录id');
			$table->bigInteger('pts_id')->nullable()->comment('绩效模板维度id');
			$table->string('ptq_id', 100)->nullable()->comment('绩效模板维度下的指标id（2,3）');
			$table->string('completion_value', 50)->nullable()->comment('完成值（2|3）');
			$table->string('completion_rate', 50)->nullable()->comment('完成率（25|30）');
			$table->string('score', 50)->nullable()->comment('分值（50|30）');
			$table->boolean('status')->nullable()->default(0)->comment('审核状态 0未打分  1打分成功 2审核驳回');
			$table->timestamps();
			$table->integer('total_score')->nullable()->default(0)->comment('总积分');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('performance_application_son');
	}

}
