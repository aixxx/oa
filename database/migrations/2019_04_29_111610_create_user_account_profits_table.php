<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAccountProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_profits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->notnull()->comment('用户id');
            $table->integer('account_type_id')->comment('收益类型 1:投资 2:工资 3:分红');
            $table->integer('balance')->length(11)->comment('收益金额 单位分');
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
        Schema::dropIfExists('user_account_profits');
    }
}
