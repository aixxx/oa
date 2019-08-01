<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMessageAddReadstatusClomn extends Migration
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
            $table->integer('sender_status')->nullable()->default(0)->comment('发送者状态1、删除2、标星。。');
            $table->integer('receiver_status')->nullable()->default(0)->comment('发送者状态1、删除2、标星。。');
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
            $table->dropColumn('sender_status');
            $table->dropColumn('receiver_status');
        });
    }
}
