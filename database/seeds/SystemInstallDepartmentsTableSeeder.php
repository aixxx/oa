<?php

use Illuminate\Database\Seeder;

class SystemInstallDepartmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('departments')->delete();
        
        \DB::table('departments')->insert(array (
            0 => 
            array (
                'auto_id' => 1,
                'id' => 1,
                'name' => '云饰集团',
                'parent_id' => 0,
                'order' => 1,
                'deepth' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_sync_wechat' => 0,
                'deleted_at' => NULL,
                'tel' => NULL,
                'attendance_id' => 1,
            ),
            1 => 
            array (
                'auto_id' => 72,
                'id' => 2,
                'name' => '鹤瀚网络',
                'parent_id' => 1,
                'order' => 2,
                'deepth' => NULL,
                'created_at' => '2019-06-17 16:44:36',
                'updated_at' => '2019-06-17 16:44:47',
                'is_sync_wechat' => 0,
                'deleted_at' => '2019-06-17 16:44:47',
                'tel' => NULL,
                'attendance_id' => 1,
            ),
        ));
        
        
    }
}