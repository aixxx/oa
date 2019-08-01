<?php

use Illuminate\Database\Seeder;

class SystemInstallWorkflowProcessVarTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('workflow_process_var')->delete();
        
        \DB::table('workflow_process_var')->insert(array (
            0 => 
            array (
                'id' => 107,
                'process_id' => 220,
                'flow_id' => 35,
                'expression_field' => 'applyer_dept_lvl',
            ),
            1 => 
            array (
                'id' => 108,
                'process_id' => 220,
                'flow_id' => 35,
                'expression_field' => 'select1',
            ),
            2 => 
            array (
                'id' => 109,
                'process_id' => 220,
                'flow_id' => 35,
                'expression_field' => 'is_leader',
            ),
            3 => 
            array (
                'id' => 110,
                'process_id' => 220,
                'flow_id' => 35,
                'expression_field' => 'expense_amount',
            ),
            4 => 
            array (
                'id' => 111,
                'process_id' => 221,
                'flow_id' => 35,
                'expression_field' => 'expense_amount',
            ),
            5 => 
            array (
                'id' => 112,
                'process_id' => 220,
                'flow_id' => 35,
                'expression_field' => 'applicant_chinese_name',
            ),
            6 => 
            array (
                'id' => 113,
                'process_id' => 249,
                'flow_id' => 42,
                'expression_field' => 'applyer_dept_lvl',
            ),
            7 => 
            array (
                'id' => 114,
                'process_id' => 249,
                'flow_id' => 42,
                'expression_field' => 'select1',
            ),
            8 => 
            array (
                'id' => 115,
                'process_id' => 249,
                'flow_id' => 42,
                'expression_field' => 'is_leader',
            ),
            9 => 
            array (
                'id' => 116,
                'process_id' => 249,
                'flow_id' => 42,
                'expression_field' => 'expense_amount',
            ),
            10 => 
            array (
                'id' => 117,
                'process_id' => 250,
                'flow_id' => 42,
                'expression_field' => 'expense_amount',
            ),
            11 => 
            array (
                'id' => 118,
                'process_id' => 249,
                'flow_id' => 42,
                'expression_field' => 'applicant_chinese_name',
            ),
            12 => 
            array (
                'id' => 119,
                'process_id' => 254,
                'flow_id' => 43,
                'expression_field' => 'applyer_dept_lvl',
            ),
            13 => 
            array (
                'id' => 120,
                'process_id' => 254,
                'flow_id' => 43,
                'expression_field' => 'select1',
            ),
            14 => 
            array (
                'id' => 121,
                'process_id' => 254,
                'flow_id' => 43,
                'expression_field' => 'is_leader',
            ),
            15 => 
            array (
                'id' => 122,
                'process_id' => 254,
                'flow_id' => 43,
                'expression_field' => 'expense_amount',
            ),
            16 => 
            array (
                'id' => 123,
                'process_id' => 255,
                'flow_id' => 43,
                'expression_field' => 'expense_amount',
            ),
            17 => 
            array (
                'id' => 124,
                'process_id' => 254,
                'flow_id' => 43,
                'expression_field' => 'applicant_chinese_name',
            ),
            18 => 
            array (
                'id' => 125,
                'process_id' => 259,
                'flow_id' => 44,
                'expression_field' => 'applyer_dept_lvl',
            ),
            19 => 
            array (
                'id' => 126,
                'process_id' => 259,
                'flow_id' => 44,
                'expression_field' => 'select1',
            ),
            20 => 
            array (
                'id' => 127,
                'process_id' => 259,
                'flow_id' => 44,
                'expression_field' => 'is_leader',
            ),
            21 => 
            array (
                'id' => 128,
                'process_id' => 259,
                'flow_id' => 44,
                'expression_field' => 'expense_amount',
            ),
            22 => 
            array (
                'id' => 129,
                'process_id' => 260,
                'flow_id' => 44,
                'expression_field' => 'expense_amount',
            ),
            23 => 
            array (
                'id' => 130,
                'process_id' => 259,
                'flow_id' => 44,
                'expression_field' => 'applicant_chinese_name',
            ),
            24 => 
            array (
                'id' => 131,
                'process_id' => 264,
                'flow_id' => 45,
                'expression_field' => 'applyer_dept_lvl',
            ),
            25 => 
            array (
                'id' => 132,
                'process_id' => 264,
                'flow_id' => 45,
                'expression_field' => 'select1',
            ),
            26 => 
            array (
                'id' => 133,
                'process_id' => 264,
                'flow_id' => 45,
                'expression_field' => 'is_leader',
            ),
            27 => 
            array (
                'id' => 134,
                'process_id' => 264,
                'flow_id' => 45,
                'expression_field' => 'expense_amount',
            ),
            28 => 
            array (
                'id' => 135,
                'process_id' => 265,
                'flow_id' => 45,
                'expression_field' => 'expense_amount',
            ),
            29 => 
            array (
                'id' => 136,
                'process_id' => 264,
                'flow_id' => 45,
                'expression_field' => 'applicant_chinese_name',
            ),
        ));
        
        
    }
}