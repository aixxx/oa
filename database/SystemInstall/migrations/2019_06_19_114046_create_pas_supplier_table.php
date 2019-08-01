<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasSupplierTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pas_supplier', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('用户编号');
			$table->string('code', 100)->nullable()->comment('供应商编号');
			$table->string('title', 100)->nullable()->comment('供应商名称');
			$table->string('mnemonic', 30)->nullable()->comment('助记符');
			$table->string('address', 100)->nullable()->comment('单位地址');
			$table->string('tel', 20)->nullable()->comment('单位电话');
			$table->string('fax', 20)->nullable()->comment('传真');
			$table->string('opening_bank', 50)->nullable()->comment('开户行');
			$table->string('number', 50)->nullable()->comment('开户行账号');
			$table->string('corporations', 20)->nullable()->comment('法人');
			$table->string('contacts', 20)->nullable()->comment('联系人');
			$table->string('phone', 20)->nullable()->comment('联系电话');
			$table->string('zip_code', 15)->nullable()->comment('邮编');
			$table->text('remark', 65535)->nullable()->comment('备注');
			$table->boolean('type')->nullable()->default(0)->comment('类型 0 进价 1零售价 2 批发价');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0删除 1添加成功');
			$table->timestamps();
			$table->boolean('ctype')->nullable()->default(1)->comment('状态 区分经销商是 1是内部添加 2外部添加');
			$table->string('email', 30)->nullable()->comment('email');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pas_supplier');
	}

}
