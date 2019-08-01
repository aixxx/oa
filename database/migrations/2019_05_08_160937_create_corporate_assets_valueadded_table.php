<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsValueaddedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets_valueadded', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('num')->length(11)->comment('增值单号');
            $table->timestamp('valueadded_at')->nullable(true)->comment('报废时间');
            $table->integer('apply_user_id')->length(11)->comment('实际申请人ID');
            $table->integer('user_id')->length(11)->comment('增值操作人ID');
            $table->decimal('valueadded_price',10,2)->comment('增值金额');
            $table->text('remarks')->comment('备注');
            $table->integer('entry_id')->length(11)->comment('工作流ID');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `corporate_assets_valueadded` comment '公司资产增值'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets_valueadded');
    }
}
