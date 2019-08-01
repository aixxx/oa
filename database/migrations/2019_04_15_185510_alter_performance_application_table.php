<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPerformanceApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('performance_application', function (Blueprint $table) {
            $table->bigInteger('number')->comment('随机编号');
            $table->string('amonth')->length('100')->nullable()->comment('考评月份');
            $table->tinyInteger('is_status')->length(1)->nullable(true)->default(0)->comment('0表示未查看，1查看过');
            $table->string('view_password')->length('200')->nullable()->comment('查看密码');
            $table->Integer('audit_times')->length('3')->nullable()->default(0)->comment('审核次数');
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
