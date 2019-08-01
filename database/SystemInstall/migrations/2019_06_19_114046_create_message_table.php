<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('receiver_id')->comment('接收者');
			$table->integer('sender_id')->comment('发送者');
			$table->text('content', 65535)->comment('内容');
			$table->integer('status')->nullable()->default(0)->comment('消息状态 0、普通 1、举报');
			$table->integer('flag')->nullable()->default(0)->comment('消息标签 0、普通 1、标星');
			$table->integer('read_status')->nullable()->default(0)->comment('阅读状态 1、已阅读');
			$table->string('remark', 191)->nullable()->default('');
			$table->softDeletes();
			$table->timestamps();
			$table->integer('sender_status')->nullable()->default(0)->comment('发送者状态1、删除2、标星。。');
			$table->integer('receiver_status')->nullable()->default(0)->comment('发送者状态1、删除2、标星。。');
			$table->integer('type')->nullable()->default(0)->comment('信息类型，0：普通，3：投票 1：任务 5：汇报 6：审批通过 7：审批驳回  8：催办  9:绩效消息');
			$table->integer('relation_id')->comment('关联ID');
			$table->string('title')->nullable()->comment('标题');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('message');
	}

}
