<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTotalComemntsModifyColumnAddConstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('total_comment', function (Blueprint $table) {
            //
            $table->integer('type')
                ->notnull()
                ->length(5)
                ->comment('类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10汇报,11工作流)')
                ->change();
            $table->integer('entry_id')->nullable()->comment('申请编号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('total_comment', function (Blueprint $table) {
            //
            $table->integer('type')
                ->notnull()
                ->length(5)
                ->comment('类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10汇报)')
                ->change();
            $table->removeColumn('entry_id');
        });
    }
}
