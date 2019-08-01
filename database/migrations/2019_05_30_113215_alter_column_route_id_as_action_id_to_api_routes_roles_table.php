<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnRouteIdAsActionIdToApiRoutesRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_routes_roles', function (Blueprint $table) {
            //
            $table->dropColumn('route_id');
            $table->integer('action_id')->length(10)->after('title')->comment('前台ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_routes_roles', function (Blueprint $table) {
            //
        });
    }
}
