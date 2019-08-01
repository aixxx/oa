<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationExtraWorkflowPassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_extra_workflow_pass', function (Blueprint $table) {
            $table->increments('id');
            $table->string('begin_end_dates', 255)->comment('已审批为准加班日期');
            $table->integer('times')->length(11)->comment('加班时间');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('entry_id')->comment('工作流ID');
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
        Schema::dropIfExists('vacation_extra_workflow_pass');
    }
}
