<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWelfareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('welfare', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entries_id')->nullable()->comment('关联workflow_entries的id');
			$table->string('title', 100)->nullable()->comment('福利标题');
			$table->integer('promoter')->nullable()->comment('发起人');
			$table->string('content')->nullable()->comment('福利内容');
			$table->string('condition_methods', 200)->nullable()->comment('福利领取条件及方式');
			$table->integer('issuer')->nullable()->comment('发放人');
			$table->dateTime('startdate')->nullable()->comment('开始日期');
			$table->dateTime('enddate')->nullable()->comment('结束日期');
			$table->boolean('status')->nullable()->comment('审批状态：1:审核中 2：审核通过 3：已删除 4：待删除');
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
		Schema::drop('welfare');
	}

}
