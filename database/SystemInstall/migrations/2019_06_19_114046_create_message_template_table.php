<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageTemplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_template', function(Blueprint $table)
		{
			$table->integer('template_id', true)->comment('主键');
			$table->string('template_key', 45)->index('idx_template_key')->comment('模板键值');
			$table->string('template_name')->comment('名称');
			$table->string('template_type', 45)->comment('类型');
			$table->string('template_sign', 45)->comment('签名');
			$table->enum('template_push_type', array('email','wechat','system','sms'))->comment('推送方式：email-邮件，wechat-企业微信，system-系统，sms-短信');
			$table->string('template_title')->default('')->comment('模板标题');
			$table->text('template_content', 65535)->comment('模板内容');
			$table->enum('template_status', array('active','inactive'))->default('inactive')->comment('模板状态：active－可用，inactive－不可用');
			$table->string('template_memo')->default('')->comment('备注');
			$table->integer('template_created_user')->comment('创建用户');
			$table->integer('template_updated_user')->comment('更新用户');
			$table->boolean('template_deleted')->default(0)->comment('是否删除：0-未删除，1-已删除');
			$table->integer('template_deleted_user')->default(0)->comment('删除用户');
			$table->dateTime('template_deleted_at')->default('1000-01-01 00:00:00')->comment('删除时间');
			$table->dateTime('template_created_at')->nullable()->comment('创建时间');
			$table->dateTime('template_updated_at')->nullable()->comment('更新时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('message_template');
	}

}
