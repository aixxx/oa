<?php

use Illuminate\Database\Seeder;

class SystemInstallAttendanceApiTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('attendance_api')->delete();
        
        \DB::table('attendance_api')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '默认考勤组',
                'system_type' => 1,
                'classes_id' => '1',
                'weeks' => '1,2,3,4,5',
                'cycle_id' => 0,
                'clock_node' => '00:00:00',
                'add_clock_num' => 5,
                'address' => '亚太大厦',
                'clock_range' => 300,
                'wifi_title' => '',
                'head_user_id' => 0,
                'overtime_rule_id' => 1,
                'is_getout_clock' => 1,
                'created_at' => '2019-06-19 13:06:22',
                'updated_at' => '2019-06-19 13:06:22',
                'deleted_at' => NULL,
                'admin_id' => 0,
                'lng' => '121.160196',
                'lat' => '31.290842',
            ),
        ));
        
        
    }
}