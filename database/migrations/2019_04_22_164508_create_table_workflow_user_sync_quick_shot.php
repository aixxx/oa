<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWorkflowUserSyncQuickShot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_user_sync', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('apply_user_id')->comment('实际申请人id');
            $table->integer('user_id')->comment('申请人id');
            $table->tinyInteger('status')->comment('状态');
            $table->text('content_json')->comment('信息集合');
            $table->timestamp('confirm_at')->nullable()->comment('确认时间');
            $table->index('apply_user_id');
            $table->index('user_id');
            $table->index('confirm_at');
            $table->softDeletes();
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
        //
    }
}
