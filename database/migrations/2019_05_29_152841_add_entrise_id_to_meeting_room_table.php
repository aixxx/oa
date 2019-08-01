<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntriseIdToMeetingRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_room', function (Blueprint $table) {
            $table->integer('entrise_id')->nullable(true)->default('0')->comment('工作流id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meeting_room', function (Blueprint $table) {
            //
        });
    }
}
