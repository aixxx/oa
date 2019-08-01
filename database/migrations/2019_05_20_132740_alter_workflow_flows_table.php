<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWorkflowFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		 Schema::table('workflow_flows', function (Blueprint $table) {
            $table->string('icon_url')->nullable()->comment('icon 图片');
            $table->string('route_url')->nullable()->comment('路由');
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workflow_flows', function (Blueprint $table) {
            //
        });
    }
}
