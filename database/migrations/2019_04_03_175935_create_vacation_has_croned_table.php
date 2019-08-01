<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationHasCronedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_vacation_has_croned', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->notnull()->comment('用户id');
            $table->integer('type')->notnull()->comment('类型1、年假 2、其他福利');
            $table->integer('cron_date')->default(0)->notnull()->comment('上一次脚本更新的时间');
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
        Schema::dropIfExists('attendance_vacation_has_croned');
    }
}
