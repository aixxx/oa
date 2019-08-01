<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('user_id')->nullable()->comment('用户编号');
			$table->string('user_name', 100)->nullable()->comment('用户名称');
			$table->bigInteger('create_user_id')->nullable()->comment('创建者编号');
			$table->string('create_user_name', 100)->nullable()->comment('创建者名称');
			$table->bigInteger('company_id')->nullable()->comment('公司编号');
			$table->integer('renew_count')->nullable()->comment('续签次数');
			$table->boolean('probation')->nullable()->comment('试用期：1，无试用期 2，一个月 3，三个月');
			$table->boolean('contract')->nullable()->comment('合同期：1，一年 2，三年 3，五年');
			$table->bigInteger('template_id')->nullable()->comment('薪资组编号');
			$table->string('template_name', 100)->nullable()->comment('薪资组名称');
			$table->decimal('performance', 10)->nullable()->comment('绩效薪资');
			$table->decimal('salary', 10)->nullable()->comment('总薪资');
			$table->integer('probation_ratio')->nullable()->comment('试用期薪资比例：70：70%，80：80%，90：90%');
			$table->dateTime('entry_at')->nullable()->comment('入职时间');
			$table->dateTime('contract_end_at')->nullable()->comment('合同结束时间');
			$table->boolean('state')->nullable()->comment('合同状态：1，试用期，');
			$table->boolean('version')->nullable()->comment('合同版本');
			$table->timestamps();
			$table->softDeletes()->index();
			$table->boolean('status')->default(1)->comment('审批状态，默认为1：未审批，2：已审批，3：已拒绝');
			$table->integer('entry_id')->comment('入职编号');
			$table->integer('entrise_id')->comment('合同工作流编号');
			$table->integer('salary_version')->unsigned()->index()->comment('当前使用的薪资版本');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contract');
	}

}
