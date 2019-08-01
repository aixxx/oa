<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdministratorsIdToMeetingRoomTable extends Migration
{
    /**
     * Run the migrations.
     *会议室 修改添加字段
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_room', function (Blueprint $table) {
            $table->integer('administrators_id')->nullable(true)->default(0)->length(11)->comment('管理员id');
            $table->integer('department_id')->nullable(true)->default(0)->length(11)->comment('管理员部门id');
            $table->integer('small_time_lapse')->nullable(true)->default(0)->length(11)->comment('最小可预约时间段');
            $table->integer('large_time_lapse')->nullable(true)->default(0)->length(11)->comment('最大可预约时间段');
            $table->integer('predictable_scope')->nullable(true)->default(0)->length(11)->comment('可预约范围');
            $table->tinyInteger('enabled_state')->nullable(true)->default(1)->length(1)->comment('启用状态 1表示启用 2表示关闭');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meeting_room', function (Blueprint $table) {
            //
        });
    }
}
