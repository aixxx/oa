<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 64)->nullable();
			$table->string('legal_person', 12)->nullable();
			$table->string('local', 64)->nullable();
			$table->string('capital', 191)->nullable()->comment('注册资本');
			$table->timestamps();
			$table->string('code', 64)->nullable()->default('')->comment('统一社会信用代码/注册号');
			$table->string('category', 64)->nullable()->default('')->comment('类型如\'其他有限责任公司\'');
			$table->date('establishment')->nullable()->comment('成立日期');
			$table->date('business_start')->nullable()->comment('营业期限开始日');
			$table->date('business_end')->nullable()->comment('营业期限截止日');
			$table->string('registration_authority', 128)->nullable()->default('')->comment('登记机关');
			$table->date('approval_at')->nullable()->comment('核准日期');
			$table->boolean('register_status')->nullable()->default(1)->comment('登记状态(1开业、2在业、3吊销、4注销、5迁入、6迁出、7停业、8清算)');
			$table->text('scope', 65535)->nullable()->comment('经营范围');
			$table->string('contact', 128)->nullable()->default('')->comment('企业联系电话');
			$table->integer('employe_num')->nullable()->default(0)->comment('从业人数');
			$table->integer('female_num')->nullable()->default(0)->comment('其中女性从业人数');
			$table->string('email')->nullable()->default('')->comment('企业电子邮箱');
			$table->integer('parent_id')->nullable()->default(0)->comment('上级公司');
			$table->boolean('status')->default(1)->comment('状态1.有效；2.删除');
			$table->string('abbr', 191)->nullable()->comment('公司名称缩写');
			$table->text('tel', 65535)->nullable()->comment('公司电话');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('companies');
	}

}
