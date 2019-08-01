<?php

use Illuminate\Database\Seeder;

class SystemInstallWorkflowMessagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('workflow_messages')->delete();
        
        
        
    }
}