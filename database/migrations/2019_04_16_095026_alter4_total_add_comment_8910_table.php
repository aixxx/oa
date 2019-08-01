<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter4TotalAddComment8910Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('total_comment', function (Blueprint $table) {

            $table->integer('type')
                ->notnull()
                ->length(5)
                ->comment('类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10评价)')
                ->change();
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
