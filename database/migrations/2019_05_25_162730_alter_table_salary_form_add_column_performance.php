<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSalaryFormAddColumnPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_form', function (Blueprint $table) {
            $table->decimal('performance',10,2)->default(0.00)->comment('个人绩效奖金');
            $table->integer('attendance_id')->nullable()->comment('关联的考勤ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
