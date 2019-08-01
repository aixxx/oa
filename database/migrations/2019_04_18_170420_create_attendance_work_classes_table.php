<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceWorkClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{
            Schema::create('attendance_work_classes', function (Blueprint $table) {
                $table->increments('class_id');
                $table->string('class_title', 20)->comment('班值代码');
                $table->string('class_name', 64)->comment('班值名称');
                $table->string('class_begin_at', 32)->comment('上班时间');
                $table->string('class_end_at', 32)->comment('下班时间');
                $table->string('class_rest_begin_at', 32)->comment('休息开始时间');
                $table->string('class_rest_end_at', 32)->comment('休息结束时间');
                $table->tinyInteger('class_times')->comment('一日几次班值');
                $table->tinyInteger('type')->comment('所属类型(1.客服类;2.职能类;3.弹性类)');
                $table->integer('class_create_user_id')->comment('创建人');
                $table->integer('class_update_user_id')->comment('修改人');
                $table->index(['class_title']);
                /**
                 * 排班班值
                 */
                $table->softDeletes();
                $table->timestamps();
            });
        }catch (Exception $e){

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_work_classes');
    }
}
