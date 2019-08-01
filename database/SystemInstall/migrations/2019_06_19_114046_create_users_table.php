<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id')->comment('内部系统uid');
			$table->string('name', 191)->nullable()->unique()->comment('系统唯一账号名');
			$table->string('employee_num', 20)->nullable()->unique()->comment('员工编号KNxxxxxx');
			$table->string('chinese_name')->comment('中文名');
			$table->string('english_name')->nullable()->comment('英文名');
			$table->string('email')->nullable()->comment('邮箱');
			$table->string('company_id')->nullable()->comment('公司id');
			$table->bigInteger('mobile')->unique()->comment('手机号（加密）');
			$table->string('position')->comment('职位');
			$table->string('avatar')->nullable()->comment('头像');
			$table->boolean('gender')->default(0)->comment('性别(1.男;2.女;0.未知)');
			$table->boolean('isleader')->nullable()->default(0)->comment('是否高管');
			$table->string('telephone')->nullable()->comment('固定电话');
			$table->text('password', 65535)->nullable()->comment('密码（加密）');
			$table->timestamps();
			$table->date('join_at')->comment('入职时间');
			$table->dateTime('regular_at')->nullable()->comment('转正时间');
			$table->dateTime('leave_at')->nullable()->comment('离职时间');
			$table->boolean('status')->default(1)->comment('员工状态');
			$table->softDeletes();
			$table->string('remember_token', 128)->nullable()->comment('唯一token');
			$table->boolean('is_sync_wechat')->nullable()->default(1)->comment('是否要同步企业微信 0：不同步， 1：同步');
			$table->string('work_address')->nullable()->comment('工作地点');
			$table->integer('superior_leaders')->nullable()->comment('上级领导');
			$table->boolean('work_type')->nullable()->comment('班值类型(1.客服类;2.职能类;3.弹性类)');
			$table->string('work_title', 64)->nullable()->comment('班值代码(P01、P02等)');
			$table->dateTime('password_modified_at')->nullable()->comment('密码修改日期');
			$table->text('password_tips', 65535)->nullable()->comment('密码提示');
			$table->integer('cumulative_length')->nullable()->default(0)->comment('累计工龄');
			$table->string('work_name', 20)->nullable()->comment('工作状态');
			$table->char('is_person_perfect', 2)->nullable()->default(0)->comment('身份信息 0不完善 17完善');
			$table->char('is_card_perfect', 2)->nullable()->default(0)->comment('银行卡信息 0不完善 17完善');
			$table->char('is_edu_perfect', 2)->nullable()->default(0)->comment('学历信息 0不完善 17完善');
			$table->char('is_pic_perfect', 2)->nullable()->default(0)->comment('个人材料 0不完善 17完善');
			$table->char('is_family_perfect', 2)->nullable()->default(0)->comment('家庭信息 0不完善 17完善');
			$table->char('is_urgent_perfect', 2)->nullable()->default(0)->comment('紧急联系人 0不完善 17完善');
			$table->char('is_positive', 1)->nullable()->default(1)->comment('转正状态 1未转正 2 转正中  3审请描述已提交');
			$table->char('is_wage', 1)->nullable()->default(1)->comment('工资包状态 1未设置 2转正');
			$table->integer('contract_status')->default(-1)->comment('合同状态，-1：未签，1：已签');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
