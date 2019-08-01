<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCountToAttendanceApiAnomaly extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_api_anomaly', function (Blueprint $table) {
            $table->tinyInteger('is_count')->length(1)->default(0)->comment('是否统计调休时间');
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
