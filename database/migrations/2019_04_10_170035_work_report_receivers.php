<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkReportReceivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('work_report_receivers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->increments('id');
            $table->unsignedInteger('report_id')->default(0)->comment('汇报id');
            $table->unsignedInteger('receiver_id')->default(0)->comment('接收者id');
            $table->unsignedTinyInteger('is_read')->default(0)->comment('0-未读 1-已读');
            $table->unsignedInteger('created_at')->default(0)->comment('创建时间');
            $table->unsignedInteger('updated_at')->default(0)->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('work_report_receivers');
    }
}
