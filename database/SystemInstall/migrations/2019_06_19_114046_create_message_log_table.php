<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_log', function(Blueprint $table)
		{
			$table->increments('id')->comment('主键');
			$table->string('template_key', 45)->index('idx_template_key')->comment('模板键值');
			$table->string('push_type', 45)->comment('推送类型');
			$table->char('sent_content_md5', 32)->comment('消息内容摘要');
			$table->text('sent_to', 65535)->comment('发送用户');
			$table->text('sent_cc', 65535)->comment('抄送用户');
			$table->boolean('sent_status')->default(0)->comment('状态：0（未发送），1（发送成功），-1（发送失败）');
			$table->dateTime('sent_at')->default('1000-01-01 00:00:00')->index('idx_sent_at')->comment('发送时间');
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
		Schema::drop('message_log');
	}

}
