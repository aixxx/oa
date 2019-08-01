<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationOutsideRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_outside_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->comment('用户id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('entry_id')->comment('工作流申请id');
            $table->timestamp('begin_time')->comment('开始时间');
            $table->timestamp('end_time')->comment('结束时间');
            $table->integer('time_sub_by_hour')->comment('时长(小时)');
            $table->text('reson')->comment('外出事由');
            $table->string('file_upload')->nullable()->comment('附件');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacation_outside_record');
    }
}
