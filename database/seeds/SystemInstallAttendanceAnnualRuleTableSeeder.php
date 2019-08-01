<?php

use Illuminate\Database\Seeder;

class SystemInstallAttendanceAnnualRuleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('attendance_annual_rule')->delete();
        
        \DB::table('attendance_annual_rule')->insert(array (
            0 => 
            array (
                'id' => 1,
                'min' => 0,
                'max' => 1,
                'value' => 5,
                'type' => 1,
                'description' => '1年以下',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'min' => 1,
                'max' => 2,
                'value' => 6,
                'type' => 1,
                'description' => '1至2年',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'min' => 2,
                'max' => 3,
                'value' => 7,
                'type' => 1,
                'description' => '2至3年',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'min' => 3,
                'max' => 4,
                'value' => 8,
                'type' => 1,
                'description' => '3至4年',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'min' => 0,
                'max' => 1,
                'value' => 5,
                'type' => 2,
                'description' => '一年以下',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'min' => 4,
                'max' => 5,
                'value' => 9,
                'type' => 1,
                'description' => '4到5年',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}