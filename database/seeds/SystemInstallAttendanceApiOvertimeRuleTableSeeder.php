<?php

use Illuminate\Database\Seeder;

class SystemInstallAttendanceApiOvertimeRuleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('attendance_api_overtime_rule')->delete();
        
        \DB::table('attendance_api_overtime_rule')->insert(array (
            0 => 
            array (
                'id' => 1,
                'is_working_overtime' => 1,
                'working_overtime_type' => 3,
                'working_begin_time' => 60,
                'working_min_overtime' => 60,
                'is_rest_overtime' => 1,
                'rest_overtime_type' => 1,
                'rest_min_overtime' => 60,
                'created_at' => '2019-06-19 13:06:22',
                'updated_at' => '2019-06-19 13:06:22',
                'deleted_at' => NULL,
                'title' => '',
            ),
        ));
        
        
    }
}