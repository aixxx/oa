<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsUseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets_use', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('num')->length(11)->comment('领用单号');
            $table->integer('apply_user_id')->length(11)->comment('实际申请人ID');
            $table->integer('user_id')->length(11)->comment('领用人ID');
            $table->text('remarks')->comment('备注');
            $table->integer('entry_id')->length(11)->comment('工作流ID');
            $table->timestamps();
            $table->softDeletes();//技术
        });
        DB::statement("ALTER TABLE `corporate_assets_use` comment '公司资产领用'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets_use');
    }
}
