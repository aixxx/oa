<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLngLatToAttendanceApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_api', function (Blueprint $table) {
            $table->string('lng' , 20)->comment('经度');
            $table->string('lat' , 20)->comment('纬度');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_api', function (Blueprint $table) {
            //
        });
    }
}
