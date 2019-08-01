<?php

use Illuminate\Database\Seeder;

class SystemInstallRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'administrator',
                'title' => 'Administrator',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-07-26 15:05:28',
                'updated_at' => '2018-07-26 15:05:28',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'HR_manager',
                'title' => 'H r manager',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-07-26 15:05:28',
                'updated_at' => '2018-07-26 15:05:28',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'test_workflow',
                'title' => '测试工作流专用',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-07-26 17:13:08',
                'updated_at' => '2018-07-26 17:20:38',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'plain_user',
                'title' => 'Plain user',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-08-09 16:23:34',
                'updated_at' => '2018-08-09 16:23:34',
            ),
            4 => 
            array (
                'id' => 6,
                'name' => 'assets_manager',
                'title' => 'Assets manager',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-09-21 11:39:48',
                'updated_at' => '2018-09-21 11:39:48',
            ),
            5 => 
            array (
                'id' => 7,
                'name' => 'developer',
                'title' => 'Developer',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-10-11 18:40:08',
                'updated_at' => '2018-10-11 18:40:08',
            ),
            6 => 
            array (
                'id' => 8,
                'name' => 'workflow_manager',
                'title' => 'Workflow manager',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-10-11 18:40:08',
                'updated_at' => '2018-10-11 18:40:08',
            ),
            7 => 
            array (
                'id' => 9,
                'name' => 'legal_manager',
                'title' => 'Legal manager',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-10-11 18:40:08',
                'updated_at' => '2018-10-11 18:40:08',
            ),
            8 => 
            array (
                'id' => 10,
                'name' => 'finance_manager',
                'title' => 'Finance manager',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-11-19 22:38:04',
                'updated_at' => '2018-11-19 22:38:04',
            ),
            9 => 
            array (
                'id' => 13,
                'name' => 'ALLEN_ROLE',
                'title' => '读文章111',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-11-27 14:46:07',
                'updated_at' => '2018-11-27 14:46:07',
            ),
            10 => 
            array (
                'id' => 15,
                'name' => 'ALLEN_ROLE',
                'title' => '读文章1113333',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-11-27 14:47:18',
                'updated_at' => '2018-11-27 14:47:18',
            ),
            11 => 
            array (
                'id' => 19,
                'name' => 'staff_book_read2313123',
                'title' => 'Name123123123123',
                'level' => NULL,
                'scope' => NULL,
                'created_at' => '2018-12-04 17:34:27',
                'updated_at' => '2018-12-04 17:34:27',
            ),
        ));
        
        
    }
}