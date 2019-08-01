<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWorkflowUserSyncComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('workflow_user_sync', function (Blueprint $table) {
            $table->integer('status')->default(0)->length(3)->comment('状态，1：待入职，2：待合同，3：待转正，4：工资包，5：待离职，6：合同到期')->change();
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
