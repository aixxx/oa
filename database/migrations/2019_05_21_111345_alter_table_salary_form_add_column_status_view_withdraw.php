<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSalaryFormAddColumnStatusViewWithdraw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_form', function (Blueprint $table) {
            $table->tinyInteger('is_view')->default(0)->comment('是否查看过,0:未查看,1:已查看');
            $table->tinyInteger('is_confirm')->default(0)->comment('是否确认,0:未确认,1:已确认');
            $table->tinyInteger('is_withdraw')->default(0)->comment('是否撤销,0:未撤销,1:已撤销');
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
