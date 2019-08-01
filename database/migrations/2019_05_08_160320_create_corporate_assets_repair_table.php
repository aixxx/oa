<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsRepairTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets_repair', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('num')->length(11)->comment('送修单号');
            $table->integer('apply_user_id')->length(11)->comment('实际申请人ID');
            $table->integer('user_id')->length(11)->comment('送修人ID');
            $table->integer('repair_day')->length(11)->comment('送修时长（天）');
            $table->string('repair_company_name')->length(255)->comment('送修单位名称');
            $table->decimal('repair_cost',10,2)->comment('送修费用');
            $table->text('remarks')->comment('备注');
            $table->integer('entry_id')->length(11)->comment('工作流ID');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `corporate_assets_repair` comment '公司资产送修'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets_repair');
    }
}
