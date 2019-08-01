<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformanceTemplateContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('performance_template_content', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->boolean('type')->nullable()->default(1)->comment('类型（1公司基础模板  2绩效模板结果）');
			$table->bigInteger('pt_id')->unsigned()->nullable()->index()->comment('绩效模板数据ID');
			$table->string('title', 50)->nullable()->comment('模板结果标题名称或是公司基础模板的id');
			$table->integer('start')->nullable()->default(0)->comment('开始值');
			$table->integer('end')->nullable()->default(0)->comment('结束值');
			$table->integer('value')->nullable()->default(0)->comment('百分比值');
			$table->boolean('status')->nullable()->default(1)->comment('0表示删除');
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
		Schema::drop('performance_template_content');
	}

}
