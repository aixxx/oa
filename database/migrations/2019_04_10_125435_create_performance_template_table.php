<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_template', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
			$table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('用户编号');
			$table->unsignedBigInteger('company_id')->nullable(true)->length(20)->comment('公司编号');
			$table->string('title')->length('100')->nullable()->comment('模板标题名称');
			$table->tinyInteger('is_status')->length(1)->nullable(true)->default(1)->comment('是否关联过结果 1是关联成功');
			$table->tinyInteger('status')->length(1)->nullable(true)->default(1)->comment('状态');
            $table->timestamps();
        });
		DB::statement("ALTER TABLE `performance_template` comment '绩效模板表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_template');
    }
}
