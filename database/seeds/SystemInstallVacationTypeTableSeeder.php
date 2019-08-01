<?php

use Illuminate\Database\Seeder;

class SystemInstallVacationTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vacation_type')->delete();
        
        \DB::table('vacation_type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'vacname' => '年假',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'vacname' => '调休',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'vacname' => '病假',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'vacname' => '婚假',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'vacname' => '陪产假',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'vacname' => '例假',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'vacname' => '事假',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'vacname' => '特假',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}