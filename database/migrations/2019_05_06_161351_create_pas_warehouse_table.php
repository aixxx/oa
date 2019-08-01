<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_warehouse', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('创建仓库用户编号');
            $table->string('title')->length('100')->nullable()->comment('仓库名称');
            $table->string('alias')->length('100')->nullable()->comment('助记码');
            $table->unsignedBigInteger('charge_id')->nullable(true)->length(20)->comment('负责人id');
            $table->string('charge_name', 100)->nullable()->comment('负责人姓名');
            $table->decimal('warehouse_area', 8, 2)->nullable(true)->comment('仓库面积');
            $table->string('address', 100)->nullable()->comment('仓库地址');
            $table->Integer('stwarehouse')->nullable(true)->default(0)->length(10)->comment('仓库货位数');
            $table->Integer('row_number')->nullable(true)->default(0)->length(10)->comment('仓库排数');
            $table->string('telephone', 20)->nullable()->comment('联系电话');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('状态 0已删除 1启用 2停用 ');
            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `pas_warehouse` comment '进销存仓库表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_warehouse');
    }
}
