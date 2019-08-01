<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUserSchedulesAddColumnPromptType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_schedules', function (Blueprint $table) {
            $table->tinyInteger("prompt_type")->length(2)->comment('提醒类型，提醒类型，0:不提醒,1：截止前15分钟，2：前1小时，3：前3小时，4：前1天');
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
