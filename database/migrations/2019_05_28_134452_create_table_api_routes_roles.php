<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableApiRoutesRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_routes_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable()->comment('名称');
            $table->integer('route_id')->comment('路由ID');
            $table->integer('role_id')->comment('角色ID');
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
