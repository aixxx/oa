<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->integer("company_id")->length(11)->comment('部门id');
            $table->timestamp('qishu')->comment('期数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            //
        });
    }
}
