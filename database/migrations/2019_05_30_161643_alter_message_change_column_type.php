<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMessageChangeColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message', function (Blueprint $table) {
            //
            $table->integer('type')->nullable()
                ->default(0)
                ->comment('信息类型，0：普通，3：投票 1：任务 5：汇报 6：审批通过 7：审批驳回  8：催办  9:绩效消息')
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
        Schema::table('message', function (Blueprint $table) {
            //
            $table->dropColumn('type');
        });
    }
}
