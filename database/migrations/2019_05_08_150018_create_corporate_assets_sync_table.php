<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateAssetsSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_assets_sync', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('apply_user_id')->length(11)->comment('实际申请人ID');
            $table->integer('assets_id')->length(11)->comment('资产ID');
            $table->integer('type')->length(5)->comment('类型，1：领用，2：借用，3：归还，4：调拨，5：送修，6：报废，7：增值，8：折旧');
            $table->integer('status')->length(5)->comment('状态，1：闲置，2：在用，3：调拨，4：维修，5：报废');
            $table->string('content_json')->length(255)->comment('信息集合');
            $table->timestamp('confirm_at')->nullable(true)->comment('确认时间');
            $table->integer('entry_id')->length(11)->comment('工作流ID');
            $table->timestamps();
            $table->softDeletes();//技术
        });

        $sql = <<<EOF
ALTER TABLE `corporate_assets_sync` add unique index(`assets_id`,`status`,`entry_id`);
EOF;
        DB::statement($sql);
        DB::statement("ALTER TABLE `corporate_assets_sync` comment '公司资产状态'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_assets_sync');
    }
}
