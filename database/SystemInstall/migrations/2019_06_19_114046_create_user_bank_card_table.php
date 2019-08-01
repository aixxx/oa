<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserBankCardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_bank_card', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->default(0)->comment('银行卡持有人');
			$table->text('card_num', 65535)->comment('银行卡号（加密）');
			$table->text('bank', 65535)->comment('开户行（加密）');
			$table->text('branch_bank', 65535)->comment('支行名称（加密');
			$table->text('bank_province', 65535)->comment('银行卡属地（省）（加密）');
			$table->text('bank_city', 65535)->comment('银行卡属地（市）（加密）');
			$table->integer('bank_type')->nullable()->comment('银行卡类型 1:主卡 2副卡');
			$table->timestamps();
			$table->string('deleted_at', 32)->nullable();
			$table->string('bank_abbr', 191)->comment('银行名称简写');
			$table->text('alipay_account', 65535)->nullable()->comment('支付宝账号');
			$table->text('wechat_account', 65535)->nullable()->comment('微信账号');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_bank_card');
	}

}
