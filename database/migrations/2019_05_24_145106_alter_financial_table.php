<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFinancialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->decimal('sum_money', 10, 2)->comment('累计金额');
            $table->decimal('cur_money', 10, 2)->comment('当前输入金额');
            $table->tinyInteger('child_status')->nullable()->comment('子财务状态');
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
            //
        });
    }
}
