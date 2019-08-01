<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExecutiveCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('executive_cars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 20)->comment('名称');
            $table->string('car_number',20)->comment('车牌号');
            $table->string('color',20)->comment('颜色');
            $table->string('brand',20)->comment('品牌');
            $table->string('type',20)->comment('类型');
            $table->string('displacement',20)->comment('排量');
            $table->integer('seat_size')->length(11)->comment('座位数量');
            $table->string('load',20)->nullable()->comment('载重');
            $table->string('fuel_type',20)->comment('燃油类型');
            $table->string('engine_number',50)->comment('发动机号');
            $table->integer('buy_money')->length(20)->nullable()->comment('购买金额');
            $table->date('buy_date')->nullable()->comment('购买日期');
            $table->tinyInteger('car_status')->length(1)->comment('车辆状态 0-空闲,1-使用中,2-维修中,3-事故中,4-报废,5-已预订');
            $table->integer('driver_id')->length(20)->comment('驾驶员');
            $table->integer('department_id')->length(11)->comment('所属部门');
            $table->integer('entrise_id')->length(11)->comment('工作流ID');
            $table->tinyInteger('status')->length(1)->default(0)->comment('审核状态');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '行政车辆 ';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
