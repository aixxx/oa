<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFinancialDetailAddLimitPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_detail', function (Blueprint $table) {
            $table->decimal('limit_price', 10, 2)->comment('报销最大金额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_detail', function (Blueprint $table) {
            //
        });
    }
}
