<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersDetailInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_detail_info', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('员工ID');
			$table->text('office_location', 65535)->nullable()->comment('办公地点（加密）');
			$table->text('note', 65535)->nullable()->comment('备注（加密）');
			$table->text('probation', 65535)->nullable()->comment('试用期（加密）');
			$table->text('after_probation', 65535)->nullable()->comment('转正日期（加密）');
			$table->text('grade', 65535)->nullable()->comment('岗位职级（加密）');
			$table->text('id_name', 65535)->nullable()->comment('身份证姓名（加密）');
			$table->text('id_number', 65535)->nullable()->comment('证件号码（加密）');
			$table->text('born_time', 65535)->nullable()->comment('出生日期（加密）');
			$table->text('ethnic', 65535)->nullable()->comment('民族（加密）');
			$table->text('id_address', 65535)->nullable()->comment('身份证地址（加密）');
			$table->text('validity_certificate', 65535)->nullable()->comment('证件有效期（加密）');
			$table->text('address', 65535)->nullable()->comment('住址（加密）');
			$table->text('first_work_time', 65535)->nullable()->comment('首次参见工作时间（加密）');
			$table->text('per_social_account', 65535)->nullable()->comment('个人社保账号（加密）');
			$table->text('per_fund_account', 65535)->nullable()->comment('个人公积金账号（加密）');
			$table->text('highest_education', 65535)->nullable()->comment('最高学历（加密）');
			$table->text('graduate_institutions', 65535)->nullable()->comment('毕业院校（加密）');
			$table->text('graduate_time', 65535)->nullable()->comment('毕业时间（加密）');
			$table->text('major', 65535)->nullable()->comment('所学专业（加密）');
			$table->text('bank_card', 65535)->nullable()->comment('银行卡号（加密）');
			$table->string('bank')->nullable()->comment('开户行（加密）');
			$table->text('contract_company', 65535)->nullable()->comment('合同公司（加密）');
			$table->text('first_contract_start_time', 65535)->nullable()->comment('首次合同起始日（加密）');
			$table->text('first_contract_end_time', 65535)->nullable()->comment('首次合同到期日（加密）');
			$table->text('cur_contract_start_time', 65535)->nullable()->comment('现合同起始日（加密）');
			$table->text('cur_contract_end_time', 65535)->nullable()->comment('现合同到期日（加密）');
			$table->text('contract_term', 65535)->nullable()->comment('合同期限（加密）');
			$table->text('renew_times', 65535)->nullable()->comment('续签次数（加密）');
			$table->text('emergency_contact', 65535)->nullable()->comment('紧急联系人姓名（加密）');
			$table->text('contact_relationship', 65535)->nullable()->comment('联系人关系（加密）');
			$table->text('contact_mobile', 65535)->nullable()->comment('联系人电话（加密）');
			$table->text('has_children', 65535)->nullable()->comment('有无子女（加密）');
			$table->text('child_name', 65535)->nullable()->comment('子女姓名（加密）');
			$table->text('child_gender', 65535)->nullable()->comment('子女性别(1.男;2.女;0.未知)（加密）');
			$table->text('child_born_time', 65535)->nullable()->comment('子女出生日期（加密）');
			$table->text('pic_id_pos', 65535)->nullable()->comment('身份证（人像面）');
			$table->text('pic_id_neg', 65535)->nullable()->comment('身份证（国徽面）');
			$table->text('pic_edu_background', 65535)->nullable()->comment('学历证书 ');
			$table->text('pic_degree', 65535)->nullable()->comment('学位证书');
			$table->text('pic_pre_company', 65535)->nullable()->comment('前公司离职证明');
			$table->string('pic_user')->nullable()->comment('员工照片 ');
			$table->string('user_type', 1)->nullable()->comment('员工状态  1正式 2试用期 3已离职');
			$table->string('user_status', 1)->nullable()->comment('员工类型 1全职 2兼职 3 劳务派遣 4劳务外包 5退休返聘 6实习');
			$table->text('census_type', 65535)->nullable()->comment('户籍类型（加密）');
			$table->text('politics_status', 65535)->nullable()->comment('政治面貌（加密）');
			$table->text('marital_status', 65535)->nullable()->comment('婚姻状况（加密）');
			$table->text('contract_type', 65535)->nullable()->comment('合同类型（加密）');
			$table->timestamps();
			$table->text('branch_bank', 65535)->nullable()->comment('支行名称（加密）');
			$table->text('bank_province', 65535)->nullable()->comment('银行卡属地：省（加密）');
			$table->text('bank_city', 65535)->nullable()->comment('银行卡属地：市（加密）');
			$table->text('gender', 65535)->nullable()->comment('性别(1.男;2.女;)');
			$table->text('alipay_account', 65535)->nullable()->comment('支付宝账号');
			$table->text('wechat_account', 65535)->nullable()->comment('微信账号');
			$table->text('nationality', 65535)->nullable()->comment('国籍');
			$table->text('id_detailed_address', 65535)->nullable()->comment('身份证详细地址（加密）');
			$table->text('detailed_address', 65535)->nullable()->comment('详细地址（加密）');
			$table->text('id_card_number', 65535)->nullable()->comment('身份证号码');
			$table->integer('certificate_type')->nullable()->comment('证件类型');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_detail_info');
	}

}
