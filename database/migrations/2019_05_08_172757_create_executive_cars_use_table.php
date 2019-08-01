<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExecutiveCarsUseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('executive_cars_use', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->comment('车辆用途');
            $table->integer('cars_id')->length(11)->comment('车辆ID');
            $table->integer('driver_id')->length(11)->comment('用车人ID');
            $table->integer('user_id')->length(11)->comment('申请人ID');
            $table->integer('department_id')->length(11)->comment('部门ID');
            $table->integer('people_number')->length(11)->comment('用车人数');
            $table->date('begin_time')->comment('用车开始时间');
            $table->date('end_time')->comment('预计返回时间');
            $table->integer('mileage')->comment('预计里程数');
            $table->string('cause', 255)->nullable()->comment('事由');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->integer('entrise_id')->length(11);
            $table->tinyInteger('status')->length(4)->comment('审核状态');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '申请用车';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('executive_cars_use');
    }
}
