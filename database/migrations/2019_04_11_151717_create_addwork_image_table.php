<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddworkImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addwork_image', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('addwork_id')->length(20)->comment('对应的加班申请id');

            $table->string('name',255)->comment('图片名字');
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
        Schema::dropIfExists('addwork_image');
    }
}
