<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assets_id')->length(11)->comment('资产ID');
            $table->integer('event_id')->length(11)->comment('资产操作ID');
            $table->integer('type')->length(5)->comment('操作类型，1：领用，2：借用，3：归还，4：调拨，5：送修，6：报废，7：增值，8：折旧');
            $table->text('remarks')->comment('备注');
            $table->integer('apply_user_id')->length(11)->comment('实际申请人ID');
            $table->integer('user_id')->length(11)->comment('用户ID');
            $table->timestamps();
            $table->softDeletes();//技术
            $table->index('assets_id');
            $table->index('event_id');
        });
        $sql = <<<EOF
ALTER TABLE `corporate_assets_relation` add unique index(`assets_id`,`event_id`);
EOF;
        DB::statement($sql);
        DB::statement("ALTER TABLE `corporate_assets_relation` comment '公司资产关联'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets_relation');
    }
}
