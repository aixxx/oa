<?php

use Illuminate\Database\Seeder;

class SystemInstallVoteTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vote_type')->delete();
        
        \DB::table('vote_type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type_name' => '生活',
                'created_at' => '2019-03-22 10:33:48',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type_name' => '工作',
                'created_at' => '2019-03-22 10:34:11',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'type_name' => '业务',
                'created_at' => '2019-03-22 10:34:25',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'type_name' => '项目',
                'created_at' => '2019-03-22 10:34:39',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'type_name' => '娱乐',
                'created_at' => '2019-03-22 10:34:52',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'type_name' => '其他',
                'created_at' => '2019-03-22 10:35:02',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}