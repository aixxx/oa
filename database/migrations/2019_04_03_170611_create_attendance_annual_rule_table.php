<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceAnnualRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_annual_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('min')->nullable(false)->comment('区间启始值');
            $table->integer('max')->nullable(false)->comment('区间结束值');
            $table->integer('value')->nullable(false)->comment('区间值');
            $table->integer('type')->nullable()->comment('年假方案');
            $table->string('description')->nullable(false)->comment('描述');
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
        Schema::dropIfExists('attendance_annual_rule');
    }
}
