<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowthRecodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('growth_recode', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->nullable()->length(11)->comment('员工id');
            $table->text('content')->nullable()->comment('记录');
            $table->text('type')->nullable()->comment('类型');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `growth_recode` comment '成长记录'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('growth_recode');
    }
}
