<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTotalAuditTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('total_audit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('type')->comment('类型(1出差，2加班，3请假，4外出)');
			$table->integer('relation_id')->comment('关联id');
			$table->integer('uid')->comment('审批人id');
			$table->string('user_name', 100)->comment('审批人姓名');
			$table->integer('status')->comment('审批状态（-1拒绝,1同意）');
			$table->timestamp('audit_time')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('审批时间');
			$table->integer('create_user_id')->comment('创建者id');
			$table->integer('is_success')->comment('是否完成 （-1作废或撤销，0默认，1完成）');
			$table->timestamps();
			$table->softDeletes()->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('total_audit');
	}

}
