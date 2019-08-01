<?php

use Illuminate\Database\Seeder;

class SystemInstallCompaniesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('companies')->delete();
        
        \DB::table('companies')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '云饰集团',
                'legal_person' => 'zhang',
                'local' => 'shanghai',
                'capital' => '10000000000',
                'created_at' => '2019-04-03 19:20:20',
                'updated_at' => '2019-04-03 19:20:24',
                'code' => '91310105MA1FWBXN5D',
                'category' => '有限责任公司分公司',
                'establishment' => '2019-04-03',
                'business_start' => '2019-04-03',
                'business_end' => '0000-00-00',
                'registration_authority' => '',
                'approval_at' => '2019-04-03',
                'register_status' => 1,
                'scope' => '从事信息科技',
                'contact' => '',
                'employe_num' => 0,
                'female_num' => 0,
                'email' => '',
                'parent_id' => 0,
                'status' => 1,
                'abbr' => NULL,
                'tel' => NULL,
            ),
        ));
        
        
    }
}