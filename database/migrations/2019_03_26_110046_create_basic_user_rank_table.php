<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasicUserRankTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_user_rank', function(Blueprint $table)
        {
            $table->bigIncrements('id')->comment('自动编号');
            $table->string('title',100)->comment('职级名称');
            $table->text('info')->comment('描述');
            $table->tinyInteger('level')->length(2)->comment('职级的权重');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '用户职级';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('basic_user_rank');
    }

}
