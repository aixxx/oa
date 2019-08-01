<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTransactionLogsAddSource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->integer("source")->length(11)->comment('来源');
			$table->timestamp('status_start_time')->comment('审核时间');
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
