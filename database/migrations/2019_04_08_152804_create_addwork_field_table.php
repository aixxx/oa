<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddworkFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addwork_field', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('p_id')->length(10)->nullable(true)->comment('类型名称');
            $table->string('name')->length(255)->nullable(true)->comment('字段名称');
            $table->string('e_name')->length(255)->nullable(true)->comment('字段的英文名称');
            $table->string('type')->length(20)->nullable(true)->comment('字段类型，例如：输入框，选择框，后台自动获取');
            $table->string('api')->length(255)->nullable(true)->comment('字段接口地址，没有则不填');
            $table->char('validate')->length(100)->nullable(true)->comment('验证');
            $table->string('status')->length(255)->nullable(true)->comment('类型(1.出差申请 2.薪资 3.加班申请 4.请假 5.外出)');
            $table->timestamps();
            $table->softDeletes();
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `addwork_field` comment '字典表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addwork_field');
    }
}
