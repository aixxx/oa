<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnEntriesIdToWorkflowUserSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflow_user_sync', function (Blueprint $table) {
            $table->integer('entry_id')->length(11)->comment('工作流ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workflow_user_sync', function (Blueprint $table) {
            //
        });
    }
}
