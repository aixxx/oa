<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskIdToSalaryRewardPunishmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_reward_punishment', function (Blueprint $table) {
            $table->integer('task_id')->comment('任务id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_reward_punishment', function (Blueprint $table) {
            //
        });
    }
}
