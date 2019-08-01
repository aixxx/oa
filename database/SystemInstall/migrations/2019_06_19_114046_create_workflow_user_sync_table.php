<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowUserSyncTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_user_sync', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('apply_user_id')->index()->comment('实际申请人id');
			$table->integer('user_id')->index()->comment('申请人id');
			$table->integer('status')->default(0)->comment('状态，1：待入职，2：待合同，3：待转正，4：工资包，5：待离职，6：合同到期');
			$table->text('content_json', 65535)->comment('信息集合');
			$table->dateTime('confirm_at')->nullable()->index()->comment('确认时间');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('entry_id')->comment('工作流ID');
			$table->unique(['user_id','status','entry_id'], 'user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('workflow_user_sync');
	}

}
