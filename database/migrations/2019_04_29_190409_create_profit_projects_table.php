<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfitProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->comment('投资项目ID');
            $table->integer('account_profits_id')->comment('用户收益项目');
            $table->string('model_name')->comment('对应的模型名称');
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
        Schema::dropIfExists('profit_projects');
    }
}
