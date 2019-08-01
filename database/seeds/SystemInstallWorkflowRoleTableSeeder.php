<?php

use Illuminate\Database\Seeder;

class SystemInstallWorkflowRoleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('workflow_role')->delete();
        
        \DB::table('workflow_role')->insert(array (
            0 => 
            array (
                'id' => 38,
                'role_name' => '部门主管',
                'created_at' => '2019-04-09 17:25:10',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            1 => 
            array (
                'id' => 39,
                'role_name' => '技术总监',
                'created_at' => '2019-04-09 17:25:41',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            2 => 
            array (
                'id' => 40,
                'role_name' => '总经理',
                'created_at' => '2019-04-09 17:26:09',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            3 => 
            array (
                'id' => 41,
                'role_name' => '财务',
                'created_at' => '2019-04-09 17:26:54',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            4 => 
            array (
                'id' => 42,
                'role_name' => '公章管理员',
                'created_at' => '2019-04-26 15:49:31',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            5 => 
            array (
                'id' => 43,
                'role_name' => '行政',
                'created_at' => '2019-05-09 17:53:30',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            6 => 
            array (
                'id' => 44,
                'role_name' => '测试主管',
                'created_at' => '2019-05-21 14:56:27',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            7 => 
            array (
                'id' => 45,
                'role_name' => '财务经理',
                'created_at' => '2019-05-22 16:26:30',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            8 => 
            array (
                'id' => 46,
                'role_name' => '会计',
                'created_at' => '2019-05-22 16:26:46',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            9 => 
            array (
                'id' => 47,
                'role_name' => '出纳',
                'created_at' => '2019-05-22 16:27:07',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            10 => 
            array (
                'id' => 48,
                'role_name' => '统计',
                'created_at' => '2019-05-22 16:27:17',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            11 => 
            array (
                'id' => 49,
                'role_name' => '副总',
                'created_at' => '2019-05-30 23:09:55',
                'updated_at' => '2019-06-03 14:00:07',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
            12 => 
            array (
                'id' => 50,
                'role_name' => '人事',
                'created_at' => '2019-06-03 17:43:20',
                'updated_at' => '2019-06-03 17:43:20',
                'deleted_at' => NULL,
                'company_id' => '1',
            ),
        ));
        
        
    }
}