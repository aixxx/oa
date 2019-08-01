<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->length(255)->comment('类型(1出差，2加班，3请假，4外出)');
            $table->timestamp('deleted_at')->comment('删除时间');
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
        Schema::dropIfExists('feedback_type');
    }
}
