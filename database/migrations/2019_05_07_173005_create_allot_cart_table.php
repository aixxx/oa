<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllotCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_allot_cart', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('warehouse_from_id')->nullable()->comment('调出仓库id');
            $table->integer('warehouse_allocation_from_id')->nullable()->comment('调出货位id');
            $table->integer('warehouse_to_id')->nullable()->comment('调入仓库id');
            $table->integer('warehouse_allocation_to_id')->nullable()->comment('调入货位id');
            $table->date('business_date')->nullable()->comment('业务日期');
            $table->text('remark')->nullable()->comment('备注');
            $table->integer('number')->nullable()->comment('合计');
            $table->integer('create_user_id')->nullable()->comment('制单人');
            $table->integer('create_user_name')->nullable()->comment('制单人姓名');
            $table->integer('cargo_user_id')->nullable()->comment('配货人');
            $table->integer('cargo_user_name')->nullable()->comment('配货人姓名');
            $table->string('delivery_type', 100)->nullable()->comment('发货方式');
            $table->integer('status')->nullable()->comment('状态');
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
        Schema::dropIfExists('pas_allot_cart');
    }
}
