<?php

use Illuminate\Database\Seeder;

class SystemInstallAttendanceApiClassesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('attendance_api_classes')->delete();
        
        \DB::table('attendance_api_classes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '默认班次',
                'code' => 'A',
                'type' => 1,
                'work_time_begin1' => '09:00:00',
                'work_time_end1' => '18:00:00',
                'work_time_begin2' => NULL,
                'work_time_end2' => NULL,
                'work_time_begin3' => NULL,
                'work_time_end3' => NULL,
                'is_siesta' => 1,
                'begin_siesta_time' => '12:00:00',
                'end_siesta_time' => '13:00:00',
                'clock_time_begin1' => NULL,
                'clock_time_end1' => NULL,
                'clock_time_begin2' => NULL,
                'clock_time_end2' => NULL,
                'clock_time_begin3' => NULL,
                'clock_time_end3' => NULL,
                'elastic_min' => 0,
                'serious_late_min' => 0,
                'absenteeism_min' => 0,
                'admin_id' => NULL,
                'created_at' => '2019-06-19 13:06:22',
                'updated_at' => '2019-06-19 13:06:22',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}