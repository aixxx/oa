<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tid')->length(11)->comment('出差id');
            $table->integer('uid')->length(11)->comment('用户id');
            $table->string('type_name')->length(255)->comment('出差城市');
            $table->tinyInteger('user_type')->length(1)->comment('用户类型 （1.审批人  2.抄送人）');
            $table->tinyInteger('level')->length(3)->comment('审批步数 (当前是第几步,抄送人是0)');
            $table->integer('create_user_id')->length(3)->comment('创建者id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_user');
    }
}
