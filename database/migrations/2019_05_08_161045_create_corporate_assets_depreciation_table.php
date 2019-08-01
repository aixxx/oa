<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsDepreciationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets_depreciation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('num')->length(11)->comment('折旧单号');
            $table->timestamp('depreciation_at')->nullable(true)->comment('折旧时间');
            $table->integer('apply_user_id')->length(11)->comment('实际申请人ID');
            $table->integer('user_id')->length(11)->comment('折旧操作人ID');
            $table->integer('apply_department_id')->length(11)->comment('实际申请人部门ID');
            $table->integer('department_id')->length(11)->comment('折旧操作人部门ID');
            $table->text('remarks')->comment('备注');
            $table->integer('entry_id')->length(11)->comment('工作流ID');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `corporate_assets_depreciation` comment '公司资产折旧'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets_depreciation');
    }
}
