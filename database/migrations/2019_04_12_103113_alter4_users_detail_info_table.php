<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter4UsersDetailInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users_detail_info', function (Blueprint $table) {
            $table->text('id_detailed_address')->nullable(true)->comment('身份证详细地址（加密）');
            $table->text('detailed_address')->nullable(true)->comment('详细地址（加密）');
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
