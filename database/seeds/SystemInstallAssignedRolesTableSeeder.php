<?php

use Illuminate\Database\Seeder;

class SystemInstallAssignedRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('assigned_roles')->delete();
        
        \DB::table('assigned_roles')->insert(array (
            0 => 
            array (
                'role_id' => 2,
                'entity_id' => 1,
                'entity_type' => 'App\\\\Models\\\\User',
                'scope' => NULL,
            ),
            1 => 
            array (
                'role_id' => 1,
                'entity_id' => 1,
                'entity_type' => 'App\\Models\\User',
                'scope' => NULL,
            ),
            2 => 
            array (
                'role_id' => 2,
                'entity_id' => 1,
                'entity_type' => 'App\\Models\\User',
                'scope' => NULL,
            ),
            3 => 
            array (
                'role_id' => 5,
                'entity_id' => 1,
                'entity_type' => 'App\\Models\\User',
                'scope' => NULL,
            ),
            4 => 
            array (
                'role_id' => 6,
                'entity_id' => 1,
                'entity_type' => 'App\\Models\\User',
                'scope' => NULL,
            ),
            5 => 
            array (
                'role_id' => 7,
                'entity_id' => 1,
                'entity_type' => 'App\\Models\\User',
                'scope' => NULL,
            ),
            6 => 
            array (
                'role_id' => 8,
                'entity_id' => 1,
                'entity_type' => 'App\\Models\\User',
                'scope' => NULL,
            ),
        ));
        
        
    }
}