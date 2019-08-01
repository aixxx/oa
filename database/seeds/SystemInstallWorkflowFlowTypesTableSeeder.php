<?php

use Illuminate\Database\Seeder;

class SystemInstallWorkflowFlowTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('workflow_flow_types')->delete();
        
        \DB::table('workflow_flow_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type_name' => '人事相关',
                'sortby' => 4500,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type_name' => '财务相关',
                'sortby' => 3000,
                'created_at' => '2018-07-26 15:05:28',
                'updated_at' => '2018-12-03 21:48:40',
            ),
            2 => 
            array (
                'id' => 3,
                'type_name' => '考勤相关',
                'sortby' => 5000,
                'created_at' => '2018-08-16 21:27:44',
                'updated_at' => '2018-08-16 21:27:44',
            ),
            3 => 
            array (
                'id' => 4,
                'type_name' => '行政相关',
                'sortby' => 4000,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}