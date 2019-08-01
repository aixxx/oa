<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('num')->length(11)->comment('调拨单号');
            $table->timestamp('transfer_at')->nullable(true)->comment('调拨时间');
            $table->integer('apply_user_id')->length(11)->comment('实际申请人ID');
            $table->integer('apply_department_id')->length(11)->comment('实际申请人部门ID');
            $table->integer('department_id')->length(11)->comment('当前使用部门');
            $table->integer('user_id')->length(11)->comment('当前使用人');
            $table->integer('transfer_to_user_id')->length(11)->comment('当前使用人');
            $table->integer('transfer_to_department_id')->length(11)->comment('当前使用人');
            $table->text('remarks')->comment('备注');
            $table->integer('entry_id')->length(11)->comment('工作流ID');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `corporate_assets_transfer` comment '公司资产调拨'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets_transfer');
    }
}
