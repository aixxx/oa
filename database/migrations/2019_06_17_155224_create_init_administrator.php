<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitAdministrator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $userData = [
            'name'        => 'super_user',
            'email'       => 'super_user@yuns.com',
            'wechat_name' => 'admin',
            'password'    => bcrypt('dfgkh123$'),
        ];
        \App\Models\Admin::updateOrCreate(['name' => 'allen'], $userData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
