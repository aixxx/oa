<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClassesIdToAttendanceApiClockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_api_clock', function (Blueprint $table) {
            $table->integer('classes_id')->length('11')->default(0)->comment('班次ID');
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
