<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleToAttendanceApiOvertimeRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_api_overtime_rule', function (Blueprint $table) {
            $table->string('title', 100)->comment('标题');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_api_overtime_rule', function (Blueprint $table) {
            //
        });
    }
}
