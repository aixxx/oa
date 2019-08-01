<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeadlineRepetitionTimeToMeetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting', function (Blueprint $table) {
            $table->dateTime('deadline')->nullable()->comment('提醒截止时间');
            $table->Integer('repetition_time')->nullable(true)->default(0)->length(11)->comment('重复时间');
            $table->Integer('frequency')->nullable(true)->default(0)->length(11)->comment('提醒次数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meeting', function (Blueprint $table) {
            //
        });
    }
}
