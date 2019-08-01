<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertVacationRuleAddColumnTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vacation_rule', function (Blueprint $table) {
            //
            $table->string('title', 255)->comment('假期名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vacation_rule', function (Blueprint $table) {
            //
            $table->dropColumn('title');
        });
    }
}
