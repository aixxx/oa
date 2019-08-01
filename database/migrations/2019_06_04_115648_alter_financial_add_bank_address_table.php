<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFinancialAddBankAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->string('bank', 200)->nullable()->comment('开户行');
			$table->string('bank_name', 200)->nullable()->comment('开户名');
			$table->string('bank_address', 200)->nullable()->comment('开户地址');
			$table->string('company_account', 100)->nullable()->comment('还款公司账户');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial', function (Blueprint $table) {
           
        });
    }
}
