<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMessageCronPushRecordsAddDiffMinutesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_cron_push_records', function (Blueprint $table) {
            //
            $table->integer('diff_minute')->nullable()->default(0)->comment('下一次执行间隔分钟数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_cron_push_records', function (Blueprint $table) {
            //
            $table->dropColumn(['diff_minute']);
        });
    }
}
