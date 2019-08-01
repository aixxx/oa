<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusToCorporateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_assets', function (Blueprint $table) {
            //
            $table->integer('status')->length(5)->comment('状态，1：闲置，2：在用，3：调拨，4：维修，5：报废');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_assets', function (Blueprint $table) {
            //
        });
    }
}
