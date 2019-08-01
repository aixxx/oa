<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersDetailInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_detail_info', function (Blueprint $table) {
            $table->char('gender',1)->nullable(true)->comment('性别(1.男;2.女;)');
            $table->text('alipay_account')->nullable(true)->comment('支付宝账号');
            $table->text('wechat_account')->nullable(true)->comment('微信账号');
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
