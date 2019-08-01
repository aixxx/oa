<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->notnull()->comment('用户id');
            $table->integer('ndays')->notnull()->comment('剩余年假时长');
            $table->integer('txdays')->notnull()->comment('剩余调休时长');
            $table->integer('cdays')->notnull()->comment('剩余产假时长');
            $table->integer('pcdays')->notnull()->comment('剩余陪产假时长');
            $table->integer('hdays')->notnull()->comment('剩余婚假时长');
            $table->integer('ldays')->notnull()->comment('剩余例假时长');
            $table->integer('sdays')->notnull()->comment('剩余丧假时长');
            $table->integer('brdays')->notnull()->comment('剩余哺乳假时长');
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
        Schema::dropIfExists('vacations');
    }
}
