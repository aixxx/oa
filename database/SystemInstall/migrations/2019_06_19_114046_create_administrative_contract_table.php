<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdministrativeContractTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('administrative_contract', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->string('title', 100)->comment('合同标题');
			$table->string('contract_number')->comment('合同编号');
			$table->string('primary_dept', 30)->nullable()->comment('所属部门');
			$table->string('contract_type', 30)->nullable()->comment('合同类型');
			$table->integer('clientId')->comment('关联工作');
			$table->integer('entry_id')->comment('审批流id');
			$table->string('secret_level', 30)->nullable()->comment('秘密等级');
			$table->string('urgency', 30)->nullable()->comment('紧急程度');
			$table->string('main_dept', 30)->nullable()->comment('主送部门');
			$table->string('copy_dept', 30)->nullable()->comment('抄送部门');
			$table->text('content', 65535)->nullable()->comment('合同内容');
			$table->string('file_upload')->comment('附件');
			$table->softDeletes();
			$table->timestamps();
			$table->char('status', 2)->nullable()->comment('-1 不同意 1 同意');
			$table->string('process_userId', 50)->nullable()->comment('流程全部员工id');
			$table->integer('user_id')->nullable()->comment('用户id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('administrative_contract');
	}

}
