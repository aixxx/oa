<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFileStorageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('file_storage', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('操作员工ID');
			$table->string('storage_full_path', 191)->comment('实际存储地址');
			$table->string('storage_system', 191)->comment('实际存储系统');
			$table->string('filehash', 191)->comment('文件hash');
			$table->string('filename', 191)->comment('文件名');
			$table->string('mime_type', 191)->comment('meta_data');
			$table->string('source_type', 191)->comment('文件来源类型');
			$table->string('source', 191)->comment('文件来源');
			$table->text('content', 65535)->nullable()->comment('内容');
			$table->timestamps();
			$table->index(['user_id','filehash']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('file_storage');
	}

}
