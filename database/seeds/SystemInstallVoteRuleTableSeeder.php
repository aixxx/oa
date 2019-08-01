<?php

use Illuminate\Database\Seeder;

class SystemInstallVoteRuleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vote_rule')->delete();
        
        \DB::table('vote_rule')->insert(array (
            0 => 
            array (
                'id' => 1,
                'rule_name' => '50%赞成为通过，一人一票',
                'is_show' => 2,
                'passing_rate' => 50,
                'vote_number' => 3,
                'job_grade' => 8,
                'created_at' => '2019-03-23 15:20:17',
                'updated_at' => '0000-00-00 00:00:00',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'rule_name' => '67%赞成为通过，一人一票',
                'is_show' => 2,
                'passing_rate' => 67,
                'vote_number' => 1,
                'job_grade' => NULL,
                'created_at' => '2019-03-23 15:20:45',
                'updated_at' => '0000-00-00 00:00:00',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}