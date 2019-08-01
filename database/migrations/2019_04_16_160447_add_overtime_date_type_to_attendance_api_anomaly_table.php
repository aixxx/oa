<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOvertimeDateTypeToAttendanceApiAnomalyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_api_anomaly', function (Blueprint $table) {
            $table->tinyInteger('overtime_date_type')->length(1)->default(1)
                ->comment('加班日期类型。1-正常工作日，2-周末，3-节假日');
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
