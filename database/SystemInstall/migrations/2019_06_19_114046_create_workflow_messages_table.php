<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkflowMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workflow_messages', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('title', 128)->nullable()->comment('标题');
			$table->text('content', 65535)->nullable()->comment('内容');
			$table->enum('type', array('mail','wechat','system'))->default('mail')->comment('消息类型');
			$table->string('sender', 128)->nullable()->comment('发送方');
			$table->string('receiver', 128)->nullable()->comment('接收方');
			$table->text('carbon_copy', 65535)->nullable()->comment('抄送方');
			$table->enum('status', array('unfinished','finished'))->default('unfinished')->comment('消息状态');
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
		Schema::drop('workflow_messages');
	}

}
