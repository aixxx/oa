<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiClockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_clock', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->integer('user_id')->length(11)->comment('用户ID');
            $table->date('clock_node')->comment('打卡日期');
            $table->dateTime('datetimes')->comment('打卡时间');
            $table->string('remark')->length(255)->nullable()->comment('备注');
            $table->string('remark_image')->length(255)->nullable()->comment('备注图片');
            $table->tinyInteger('type')->length(1)->comment('打卡类型 1-上班 2-下班');
            $table->string('clock_address_type')->length(255)->nullable()->comment('打卡地址类型， 1- 公司内打卡 2-外勤打卡 3-出差打卡');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '考勤打卡';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_clock');
    }
}
