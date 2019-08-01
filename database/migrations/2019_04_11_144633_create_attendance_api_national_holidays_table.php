<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiNationalHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_national_holidays', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->date('dates')->comment('日期');
            $table->tinyInteger('type')->length(1)->comment('类型 1-工作日改为休息  2-休息日改为上班');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '国家法定节假日';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_national_holidays');
    }
}
