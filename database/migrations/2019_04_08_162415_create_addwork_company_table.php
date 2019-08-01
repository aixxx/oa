<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddworkCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addwork_company', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->integer('company_id')->length(10)->nullable(true)->comment('公司编号');
            $table->integer('field_id')->length(10)->nullable(true)->comment('字段编号');
            $table->tinyInteger('type')->length(4)->nullable(true)->comment('1:出差，2：薪资，3：加班，4：请假');
            $table->timestamps();
            $table->softDeletes();
            $table->index('field_id');
            $table->index('deleted_at');
        });
        DB::statement("ALTER TABLE `addwork_company` comment '公司字典关联表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addwork_company');
    }
}
