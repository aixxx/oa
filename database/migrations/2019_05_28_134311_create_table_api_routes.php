<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableApiRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path')->nullable()->comment('路由');
            $table->string('title')->nullable()->comment('名称');
            $table->integer('parent_id')->comment('父ID');
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
