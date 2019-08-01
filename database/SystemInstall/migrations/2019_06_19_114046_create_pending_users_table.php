<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePendingUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pending_users', function(Blueprint $table)
		{
			$table->increments('id')->comment('内部系统uid');
			$table->string('given_name')->comment('中文-名');
			$table->string('family_name')->comment('中文-姓');
			$table->string('email')->comment('邮箱');
			$table->text('mobile', 65535)->comment('手机号（加密）');
			$table->string('position')->comment('职位');
			$table->boolean('gender')->default(0)->comment('性别(1.男;2.女;0.未知)');
			$table->timestamps();
			$table->dateTime('join_at')->nullable()->comment('入职时间');
			$table->boolean('status')->default(0);
			$table->softDeletes();
			$table->integer('company_id')->default(1)->comment('所属公司id');
			$table->integer('department_id')->default(1)->comment('所属部门id');
			$table->string('english_name')->comment('英文名');
			$table->boolean('is_leader')->default(0)->comment('是否是高管');
			$table->boolean('is_sync_wechat')->default(1)->comment('是否要同步企业微信');
			$table->string('name', 191)->unique()->comment('企业微信账号名 例如allenwang');
			$table->string('work_address')->nullable()->comment('工作地点');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pending_users');
	}

}
