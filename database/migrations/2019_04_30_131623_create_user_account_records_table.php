<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAccountRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_records', function (Blueprint $table) {
            $table->increments('id');
	     $table->integer('user_id')->notnull()->comment('用户id');
	     $table->string('title')->comment('消费项目标题');
		 $table->string('sub')->comment('副标题');
	     $table->tinyInteger('is_correlation_model')->length(4)->comment('是否关联模型');
	     $table->integer('model_id')->length(11)->comment('模型ID');
            $table->string('model_name')->length(50)->comment('模型名称');
            $table->integer('account_type_id')->comment('收益类型 1:投资 2:工资 3:分红 -1:支出');
            $table->integer('balance')->length(11)->comment('收益金额 单位分');
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
        Schema::dropIfExists('user_account_records');
    }
}
