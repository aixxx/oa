<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiCycleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_cycle', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->tinyInteger('type')->length(1)->comment('类型 1-做一休一 2-两班轮换 3-三班倒');
            $table->string('title')->length(50)->comment('周期名称');
            $table->integer('cycle_days')->length(11)->comment('周期天数');
            $table->integer('admin_id')->length(11)->comment('操作人员ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '排班制周期表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_cycle');
    }
}
