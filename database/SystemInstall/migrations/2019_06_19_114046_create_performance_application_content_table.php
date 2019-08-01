<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformanceApplicationContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('performance_application_content', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->boolean('type')->nullable()->default(1)->comment('类型1.薪资绩效申请');
			$table->bigInteger('pa_id')->unsigned()->nullable()->comment('绩效申请记录id');
			$table->bigInteger('pt_id')->unsigned()->nullable()->comment('绩效基础表id');
			$table->integer('sum')->nullable()->comment('绩效模板中各个配置项所占百分比');
			$table->integer('value')->nullable()->comment('保存的值（100）%');
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
		Schema::drop('performance_application_content');
	}

}
