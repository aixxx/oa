<?php

use Illuminate\Database\Seeder;

class SystemInstallUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'employee_num' => '1',
                'chinese_name' => 'admin',
                'english_name' => 'henryxu',
                'email' => 'admin@126.com',
                'company_id' => '1',
                'mobile' => '15253029839',
                'position' => 'CEO',
                'avatar' => 'http://aikeerp.oss-cn-shanghai.aliyuncs.com/2019-05%2F28%2Fff2525b3078eb44c44ecd4fd164cde47.jpeg?OSSAccessKeyId=LTAIk0ALGctA12hm&Signature=lnAU5aqR0ZrTxGuF4lyvZ1r5bn4%3D&Expires=1748403495',
                'gender' => 1,
                'isleader' => 0,
                'telephone' => '1',
                'password' => '$2y$10$N..CndL0XwX39VGe.ocYTOZXubnR5PNdiR3kNlV9BSGW6OW6Df1ca',
                'created_at' => NULL,
                'updated_at' => '2019-06-17 17:41:26',
                'join_at' => '2019-03-01',
                'regular_at' => NULL,
                'leave_at' => NULL,
                'status' => 1,
                'deleted_at' => NULL,
                'remember_token' => '3G8RQSnWLWuLvO2UDO2qCUqCsb2j9cwb46lInJWRfPASEBe5QYwjyjPldqhg',
                'is_sync_wechat' => 1,
                'work_address' => 'shanghai',
                'superior_leaders' => NULL,
                'work_type' => 1,
                'work_title' => '',
                'password_modified_at' => NULL,
                'password_tips' => NULL,
                'cumulative_length' => 0,
                'work_name' => NULL,
                'is_person_perfect' => '0',
                'is_card_perfect' => '0',
                'is_edu_perfect' => '0',
                'is_pic_perfect' => '0',
                'is_family_perfect' => '0',
                'is_urgent_perfect' => '0',
                'is_positive' => '1',
                'is_wage' => '1',
                'contract_status' => -1,
            ),
        ));
        
        
    }
}