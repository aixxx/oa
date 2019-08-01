<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescribeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('describe', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户');
            $table->string('positive_please')->nullable()->comment('审请描述');
            $table->string('wage_classes')->nullable()->comment('工资包名称');
            $table->string('salary_scale')->nullable()->comment('本薪比例');
            $table->string('points_scale')->nullable()->comment('分红比例');
            $table->softDeletes();
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
        Schema::dropIfExists('describe');
    }
}
