<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUinqueToWorkflowUserSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $sql = <<<EOF
ALTER TABLE `workflow_user_sync` add unique index(`user_id`,`status`,`entry_id`);
EOF;
        DB::statement($sql);
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
