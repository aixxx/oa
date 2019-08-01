<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAvatarToVoteParticipantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_participant', function (Blueprint $table) {
            //
            $table->string('avatar')->length(255)->nullable(true)->comment('用户头像');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vote_participant', function (Blueprint $table) {
            //
        });
    }
}
