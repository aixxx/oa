<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClockNumsToAttendanceApiAnomalyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_api_anomaly', function (Blueprint $table) {
            $table->tinyInteger('clock_nums')->length(1)->default(0)->comment('第几次上下班');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_api_anomaly', function (Blueprint $table) {
            //
        });
    }
}
