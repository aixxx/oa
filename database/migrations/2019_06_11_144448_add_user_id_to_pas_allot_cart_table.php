<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToPasAllotCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_allot_cart', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('添加人id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_allot_cart', function (Blueprint $table) {
            //
        });
    }
}
