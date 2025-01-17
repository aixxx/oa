<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFinancialDetailAddIsIsControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_detail', function (Blueprint $table) {
            $table->tinyInteger('is_control')->nullable()->default(0)->comment('是否控制');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_detail', function (Blueprint $table) {
            //
        });
    }
}
