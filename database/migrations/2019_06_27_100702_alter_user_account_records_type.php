<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserAccountRecordsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_account_records', function (Blueprint $table) {
            $table->tinyInteger('type')->length(1)->nullable()->comment('记录类型(0:收益；1：支出)');
            $table->tinyInteger('account_type')->length(1)->nullable()->comment('收益类型(0:投资收益；1：工资收益；2：分红收益；3：报销；4：支付；5：借款；6：还款；7：收款)');
            $table->dropColumn('account_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_account_records', function (Blueprint $table) {
        });
    }
}
