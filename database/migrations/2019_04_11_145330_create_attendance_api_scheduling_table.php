<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiSchedulingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_scheduling', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->integer('attendance_id')->length(11)->comment('考勤组ID');
            $table->integer('user_id')->length(11)->comment('用户ID');
            $table->date('dates')->nullable()->comment('排班日期');
            $table->integer('classes_id')->length(11)->nullable()->comment('班次ID');
            $table->date('take_effect_dates')->nullable()->comment('生效时间日期');
            $table->integer('admin_id')->length(11)->nullable()->comment('操作人员ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '排班表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_scheduling');
    }
}
