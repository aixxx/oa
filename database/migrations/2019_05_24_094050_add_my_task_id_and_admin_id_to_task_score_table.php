<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMyTaskIdAndAdminIdToTaskScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_score', function (Blueprint $table) {
            $table->integer('my_task_id')->length(11)->default(0)->comment('我的任务ID');
            $table->integer('admin_id')->length(11)->default(0)->comment('评分人ID，0:系统');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_score', function (Blueprint $table) {
            //
        });
    }
}
