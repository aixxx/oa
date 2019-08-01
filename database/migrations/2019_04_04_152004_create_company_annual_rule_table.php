<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyAnnualRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_annual_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->notnull()->comment('公司id');
            $table->integer('rule_id')->notnull()->comment('年假规则id');
            $table->integer('type')->notnull()->comment('公司多对多关联分类1、年假规则');
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
        Schema::dropIfExists('company_annual_rule');
    }
}
