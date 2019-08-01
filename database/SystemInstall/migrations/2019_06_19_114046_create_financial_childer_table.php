<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFinancialChilderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('financial_childer', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('financial_id')->nullable()->comment('我的财务id');
			$table->string('title', 100)->nullable()->comment('标题');
			$table->integer('voucher_id')->nullable();
			$table->decimal('money', 13)->nullable();
			$table->string('status')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->index(['financial_id','deleted_at'], 'financial_deleted_index');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('financial_childer');
	}

}
