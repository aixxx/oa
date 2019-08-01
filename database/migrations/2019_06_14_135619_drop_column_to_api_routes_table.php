<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnToApiRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_routes', function (Blueprint $table) {
            //
            $table->dropForeign('api_routes_action_id_foreign');
            $table->dropColumn('action_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_routes', function (Blueprint $table) {
            //
        });
    }
}
