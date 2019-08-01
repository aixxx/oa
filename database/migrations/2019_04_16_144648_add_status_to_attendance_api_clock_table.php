<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToAttendanceApiClockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_api_clock', function (Blueprint $table) {
            $table->tinyInteger('status')->length(1)->default(1)->comment('状态。1-正常，0-无效');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_api_clock', function (Blueprint $table) {
            //
        });
    }
}
