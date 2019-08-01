<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->length(150)->comment('资产名称');
            $table->string('num')->length(150)->comment('资产编号');
            $table->integer('attr')->length(5)->comment('资产属性，1：固定资产，2：虚拟资产');
            $table->integer('cat')->length(5)->comment('资产分类，固定资产（1：计算机设备，2：办公设备，3：通信设备，4：家具用具，5：其他固定资产），虚拟资产（6：软件，7：其他虚拟资产）');
            $table->integer('source')->length(5)->comment('资产来源，1：购入，2：接收投资，3：调入，4：自建');
            $table->decimal('price', 10, 2)->comment('资产价格');
            $table->string('metering')->length(150)->comment('计量单位');
            $table->timestamp('buy_time')->nullable(true)->comment('购买时间');
            $table->integer('nature')->length(5)->comment('资产性质，1：折旧资产，2：增值资产');
            $table->integer('depreciation_cycle')->length(10)->comment('折旧周期');
            $table->integer('depreciation_interval')->length(10)->comment('折旧间隔');
            $table->integer('depreciation_method')->length(5)->comment('折旧方法，1：线性，2：递减');
            $table->string('location')->length(255)->comment('资产位置');
            $table->string('photo')->length(255)->comment('资产照片');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `corporate_assets` comment '公司资产'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets');
    }
}
