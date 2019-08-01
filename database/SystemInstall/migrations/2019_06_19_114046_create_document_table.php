<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('document', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('用户');
			$table->integer('entry_id')->comment('文件公文流id');
			$table->string('doc_title')->nullable()->comment('公文标题');
			$table->integer('status')->default(0)->comment('状态 默认 0:审批中  1:审批过');
			$table->string('document_number')->nullable()->comment('公文字号');
			$table->string('primary_dept')->nullable()->comment('所属部门');
			$table->integer('primary_dept_id')->nullable()->comment('所属部门id');
			$table->string('doc_type')->nullable()->comment('类型: 1:通知 2:公告 3:通报 4:议案 5:报告 6:请示 7:批复 8:意见 9:函 10:会议纪要');
			$table->string('secret_level')->nullable()->comment('秘密等级: 1:公开 2:秘密 3:机密 4:绝密');
			$table->string('urgency')->nullable()->comment('紧急程度: 1:普通 2:加急 3:特级');
			$table->string('subject')->nullable()->comment('主题词');
			$table->string('main_dept')->nullable()->comment('主送部门');
			$table->integer('main_dept_id')->nullable()->comment('主送部门id');
			$table->string('copy_dept')->nullable()->comment('抄送部门');
			$table->integer('copy_dept_id')->nullable()->comment('抄送部门id');
			$table->text('content', 65535)->nullable()->comment('文件内容');
			$table->string('file_upload')->nullable()->comment('上传的文件');
			$table->string('authorized_userId')->nullable()->comment('所有签批人id');
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
		Schema::drop('document');
	}

}
