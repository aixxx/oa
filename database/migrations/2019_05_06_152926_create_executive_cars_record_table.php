<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExecutiveCarsRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('executive_cars_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cars_id')->length(11)->comment('车ID');
            $table->integer('type')->length(11)->comment('记录类型,1-年检，2-保险，3-违章，4-事故，5-保养，6-维修，7-加油');
            $table->string('status', 20)->comment('车检状态');
            $table->date('dates')->comment('车检日期');
            $table->string('address', 50)->comment('车检地址');
            $table->string('append', 100)->nullable()->comment('车检附件');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '行政汽车各项记录';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('executive_cars_record');
    }
}
