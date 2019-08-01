<?php

use Illuminate\Database\Seeder;

class SystemInstallMigrationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('migrations')->delete();
        
        \DB::table('migrations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'migration' => '2016_06_01_000001_create_oauth_auth_codes_table',
                'batch' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'migration' => '2016_06_01_000002_create_oauth_access_tokens_table',
                'batch' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'migration' => '2016_06_01_000003_create_oauth_refresh_tokens_table',
                'batch' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'migration' => '2016_06_01_000004_create_oauth_clients_table',
                'batch' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'migration' => '2016_06_01_000005_create_oauth_personal_access_clients_table',
                'batch' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'migration' => '2019_04_03_170611_create_attendance_annual_rule_table',
                'batch' => 2,
            ),
            6 => 
            array (
                'id' => 7,
                'migration' => '2019_04_03_175935_create_vacation_has_croned_table',
                'batch' => 2,
            ),
            7 => 
            array (
                'id' => 8,
                'migration' => '2019_04_04_134854_alter_users_table',
                'batch' => 2,
            ),
            8 => 
            array (
                'id' => 9,
                'migration' => '2019_04_04_152004_create_company_annual_rule_table',
                'batch' => 2,
            ),
            9 => 
            array (
                'id' => 10,
                'migration' => '2019_04_04_153726_create_vote_table',
                'batch' => 2,
            ),
            10 => 
            array (
                'id' => 11,
                'migration' => '2019_03_26_110046_create_basic_user_rank_table',
                'batch' => 3,
            ),
            11 => 
            array (
                'id' => 12,
                'migration' => '2019_04_04_165550_create_vote_option_table',
                'batch' => 4,
            ),
            12 => 
            array (
                'id' => 13,
                'migration' => '2019_04_04_171857_alter_vote_option_table',
                'batch' => 5,
            ),
            13 => 
            array (
                'id' => 14,
                'migration' => '2019_04_04_172445_create_vote_participant_table',
                'batch' => 6,
            ),
            14 => 
            array (
                'id' => 15,
                'migration' => '2019_04_04_154807_alter_users_detail_info_table',
                'batch' => 7,
            ),
            15 => 
            array (
                'id' => 16,
                'migration' => '2019_04_04_161337_alter_users_table',
                'batch' => 7,
            ),
            16 => 
            array (
                'id' => 17,
                'migration' => '2019_04_04_162351_alter_user_bank_card_table',
                'batch' => 7,
            ),
            17 => 
            array (
                'id' => 18,
                'migration' => '2019_04_08_090656_create_vote_record_table',
                'batch' => 7,
            ),
            18 => 
            array (
                'id' => 19,
                'migration' => '2019_04_08_092842_create_vote_rule_table',
                'batch' => 7,
            ),
            19 => 
            array (
                'id' => 20,
                'migration' => '2019_04_08_094225_create_vote_type_table',
                'batch' => 7,
            ),
            20 => 
            array (
                'id' => 21,
                'migration' => '2019_03_28_144824_create_basic_oa_type_table',
                'batch' => 8,
            ),
            21 => 
            array (
                'id' => 22,
                'migration' => '2019_03_28_145932_create_basic_oa_option_table',
                'batch' => 8,
            ),
            22 => 
            array (
                'id' => 23,
                'migration' => '2019_04_08_141044_alter1_user_table',
                'batch' => 9,
            ),
            23 => 
            array (
                'id' => 26,
                'migration' => '2019_04_08_164119_create_users_salary_template_table',
                'batch' => 9,
            ),
            24 => 
            array (
                'id' => 27,
                'migration' => '2019_04_08_171751_create_users_salary_relation_table',
                'batch' => 9,
            ),
            25 => 
            array (
                'id' => 28,
                'migration' => '2019_04_08_172952_create_users_salary_data_table',
                'batch' => 9,
            ),
            26 => 
            array (
                'id' => 29,
                'migration' => '2019_04_09_124624_alter_departments_table',
                'batch' => 9,
            ),
            27 => 
            array (
                'id' => 30,
                'migration' => '2019_04_09_145052_alter_users_detail_info_table',
                'batch' => 9,
            ),
            28 => 
            array (
                'id' => 31,
                'migration' => '2019_04_10_162648_work_reports',
                'batch' => 10,
            ),
            29 => 
            array (
                'id' => 32,
                'migration' => '2019_04_10_170035_work_report_receivers',
                'batch' => 10,
            ),
            30 => 
            array (
                'id' => 33,
                'migration' => '2019_04_10_150656_create_leaveout_table',
                'batch' => 11,
            ),
            31 => 
            array (
                'id' => 34,
                'migration' => '2019_04_10_161641_create_leaveout_program_table',
                'batch' => 11,
            ),
            32 => 
            array (
                'id' => 35,
                'migration' => '2019_04_10_183539_create_leaveout_img_table',
                'batch' => 11,
            ),
            33 => 
            array (
                'id' => 36,
                'migration' => '2019_04_10_184213_create_total_audit_table',
                'batch' => 11,
            ),
            34 => 
            array (
                'id' => 37,
                'migration' => '2019_04_10_185545_create_task_table',
                'batch' => 11,
            ),
            35 => 
            array (
                'id' => 38,
                'migration' => '2019_04_11_091359_create_total_comment_table',
                'batch' => 12,
            ),
            36 => 
            array (
                'id' => 39,
                'migration' => '2019_04_04_154807_alter1_users_detail_info_table',
                'batch' => 13,
            ),
            37 => 
            array (
                'id' => 40,
                'migration' => '2019_04_04_161337_alter2_users_table',
                'batch' => 14,
            ),
            38 => 
            array (
                'id' => 41,
                'migration' => '2019_04_09_102008_create_contract_table',
                'batch' => 14,
            ),
            39 => 
            array (
                'id' => 42,
                'migration' => '2019_04_09_150928_create_cron_push_records_table',
                'batch' => 14,
            ),
            40 => 
            array (
                'id' => 43,
                'migration' => '2019_04_10_125435_create_performance_template_table',
                'batch' => 14,
            ),
            41 => 
            array (
                'id' => 44,
                'migration' => '2019_04_10_125447_create_performance_template_content_table',
                'batch' => 14,
            ),
            42 => 
            array (
                'id' => 45,
                'migration' => '2019_04_10_133124_alter_users_table_set_somecolumns_default_null',
                'batch' => 14,
            ),
            43 => 
            array (
                'id' => 46,
                'migration' => '2019_04_10_165046_create_vacations_table',
                'batch' => 14,
            ),
            44 => 
            array (
                'id' => 47,
                'migration' => '2019_04_10_172153_create_vacation_type_table',
                'batch' => 14,
            ),
            45 => 
            array (
                'id' => 48,
                'migration' => '2019_04_10_172445_create_feedback_content_table',
                'batch' => 14,
            ),
            46 => 
            array (
                'id' => 49,
                'migration' => '2019_04_10_173044_create_leave_table',
                'batch' => 14,
            ),
            47 => 
            array (
                'id' => 50,
                'migration' => '2019_04_10_174903_create_feedback_accssory_table',
                'batch' => 14,
            ),
            48 => 
            array (
                'id' => 51,
                'migration' => '2019_04_10_175400_create_examined_copy_table',
                'batch' => 14,
            ),
            49 => 
            array (
                'id' => 52,
                'migration' => '2019_04_10_182556_create_company_leave_unit_table',
                'batch' => 14,
            ),
            50 => 
            array (
                'id' => 53,
                'migration' => '2019_04_10_183538_create_feedback_reply_table',
                'batch' => 14,
            ),
            51 => 
            array (
                'id' => 54,
                'migration' => '2019_04_10_194125_create_my_task_table',
                'batch' => 14,
            ),
            52 => 
            array (
                'id' => 55,
                'migration' => '2019_04_11_091412_create_feedback_type_table',
                'batch' => 14,
            ),
            53 => 
            array (
                'id' => 56,
                'migration' => '2019_04_11_094446_create_addwork_audit_peoples_table',
                'batch' => 14,
            ),
            54 => 
            array (
                'id' => 57,
                'migration' => '2019_04_11_095053_create_addwork_table',
                'batch' => 14,
            ),
            55 => 
            array (
                'id' => 58,
                'migration' => '2019_04_11_095546_alert1_feedback_content_table',
                'batch' => 14,
            ),
            56 => 
            array (
                'id' => 59,
                'migration' => '2019_04_11_100221_create_contract_approval_table',
                'batch' => 14,
            ),
            57 => 
            array (
                'id' => 60,
                'migration' => '2019_04_11_100526_alert_addwork_table',
                'batch' => 14,
            ),
            58 => 
            array (
                'id' => 61,
                'migration' => '2019_04_11_100551_create_performance_basics_table',
                'batch' => 14,
            ),
            59 => 
            array (
                'id' => 62,
                'migration' => '2019_04_11_114003_create_comments_table',
                'batch' => 14,
            ),
            60 => 
            array (
                'id' => 63,
                'migration' => '2019_04_11_131031_alter_my_task_table',
                'batch' => 14,
            ),
            61 => 
            array (
                'id' => 64,
                'migration' => '2019_04_11_131750_alter_contract_table',
                'batch' => 14,
            ),
            62 => 
            array (
                'id' => 65,
                'migration' => '2019_04_11_135347_alter1_total_comment_table',
                'batch' => 14,
            ),
            63 => 
            array (
                'id' => 66,
                'migration' => '2019_04_11_135537_create_attendance_api_table',
                'batch' => 14,
            ),
            64 => 
            array (
                'id' => 67,
                'migration' => '2019_04_11_142021_create_leave_unit_table',
                'batch' => 14,
            ),
            65 => 
            array (
                'id' => 68,
                'migration' => '2019_04_11_142226_create_attendance_api_anomaly_table',
                'batch' => 14,
            ),
            66 => 
            array (
                'id' => 69,
                'migration' => '2019_04_11_142527_alert1_addwork_table',
                'batch' => 14,
            ),
            67 => 
            array (
                'id' => 70,
                'migration' => '2019_04_11_142731_create_attendance_api_classes_table',
                'batch' => 14,
            ),
            68 => 
            array (
                'id' => 71,
                'migration' => '2019_04_11_143543_alert2_addwork_table',
                'batch' => 14,
            ),
            69 => 
            array (
                'id' => 72,
                'migration' => '2019_04_11_143547_create_attendance_api_clock_table',
                'batch' => 14,
            ),
            70 => 
            array (
                'id' => 73,
                'migration' => '2019_04_11_143923_create_attendance_api_cycle_table',
                'batch' => 14,
            ),
            71 => 
            array (
                'id' => 74,
                'migration' => '2019_04_11_144128_create_attendance_api_cycle_content_table',
                'batch' => 14,
            ),
            72 => 
            array (
                'id' => 75,
                'migration' => '2019_04_11_144515_create_attendance_api_department_table',
                'batch' => 14,
            ),
            73 => 
            array (
                'id' => 76,
                'migration' => '2019_04_11_144633_create_attendance_api_national_holidays_table',
                'batch' => 14,
            ),
            74 => 
            array (
                'id' => 77,
                'migration' => '2019_04_11_144914_create_attendance_api_overtime_rule_table',
                'batch' => 14,
            ),
            75 => 
            array (
                'id' => 78,
                'migration' => '2019_04_11_145330_create_attendance_api_scheduling_table',
                'batch' => 14,
            ),
            76 => 
            array (
                'id' => 79,
                'migration' => '2019_04_11_145635_create_attendance_api_staff_table',
                'batch' => 14,
            ),
            77 => 
            array (
                'id' => 80,
                'migration' => '2019_04_11_151717_create_addwork_image_table',
                'batch' => 14,
            ),
            78 => 
            array (
                'id' => 81,
                'migration' => '2019_04_11_155318_create_performance_application_table',
                'batch' => 14,
            ),
            79 => 
            array (
                'id' => 82,
                'migration' => '2019_04_11_162117_create_performance_application_content_table',
                'batch' => 14,
            ),
            80 => 
            array (
                'id' => 83,
                'migration' => '2019_04_11_171106_alter_total_audit_table',
                'batch' => 14,
            ),
            81 => 
            array (
                'id' => 84,
                'migration' => '2019_04_12_102435_work_report_rules',
                'batch' => 15,
            ),
            82 => 
            array (
                'id' => 85,
                'migration' => '2019_04_12_091102_alter3_users_detail_info_table',
                'batch' => 16,
            ),
            83 => 
            array (
                'id' => 86,
                'migration' => '2019_04_12_092358_alter_performance_application_table',
                'batch' => 16,
            ),
            84 => 
            array (
                'id' => 87,
                'migration' => '2019_04_12_103113_alter4_users_detail_info_table',
                'batch' => 16,
            ),
            85 => 
            array (
                'id' => 88,
                'migration' => '2019_04_08_152804_create_addwork_field_table',
                'batch' => 17,
            ),
            86 => 
            array (
                'id' => 89,
                'migration' => '2019_04_12_095828_create_trip_table',
                'batch' => 17,
            ),
            87 => 
            array (
                'id' => 90,
                'migration' => '2019_04_12_113057_create_trip_agenda_table',
                'batch' => 17,
            ),
            88 => 
            array (
                'id' => 91,
                'migration' => '2019_04_12_130556_create_trip_user_table',
                'batch' => 17,
            ),
            89 => 
            array (
                'id' => 92,
                'migration' => '2019_04_12_131455_alter5_users_detail_info_table',
                'batch' => 17,
            ),
            90 => 
            array (
                'id' => 93,
                'migration' => '2019_04_12_133418_alter_trip_user_table',
                'batch' => 17,
            ),
            91 => 
            array (
                'id' => 94,
                'migration' => '2019_04_08_162415_create_addwork_company_table',
                'batch' => 18,
            ),
            92 => 
            array (
                'id' => 95,
                'migration' => '2019_04_12_094722_create_message_table',
                'batch' => 19,
            ),
            93 => 
            array (
                'id' => 96,
                'migration' => '2019_04_12_135504_alter_vote_add_percentage_default_table',
                'batch' => 19,
            ),
            94 => 
            array (
                'id' => 97,
                'migration' => '2019_04_12_143251_create_social_security_table',
                'batch' => 19,
            ),
            95 => 
            array (
                'id' => 98,
                'migration' => '2019_04_12_154159_alter_social_security_add_field_table',
                'batch' => 19,
            ),
            96 => 
            array (
                'id' => 99,
                'migration' => '2019_04_12_162241_alter_social_security_table',
                'batch' => 19,
            ),
            97 => 
            array (
                'id' => 100,
                'migration' => '2019_04_11_175322_add_dates_to_attendance_api_clock',
                'batch' => 20,
            ),
            98 => 
            array (
                'id' => 101,
                'migration' => '2019_04_12_091342_add_clock_nums_to_attendance_api_clock',
                'batch' => 20,
            ),
            99 => 
            array (
                'id' => 102,
                'migration' => '2019_04_12_113621_add_is_count_to_attendance_api_anomaly',
                'batch' => 20,
            ),
            100 => 
            array (
                'id' => 103,
                'migration' => '2019_04_12_172525_create_punishment_template_table',
                'batch' => 20,
            ),
            101 => 
            array (
                'id' => 104,
                'migration' => '2019_04_12_193423_update_classes_id_to_attendance_api',
                'batch' => 20,
            ),
            102 => 
            array (
                'id' => 105,
                'migration' => '2019_04_13_102521_create_punishment_template_content_table',
                'batch' => 20,
            ),
            103 => 
            array (
                'id' => 106,
                'migration' => '2019_04_15_101748_alter_vote_add_colnum_vote_number_table',
                'batch' => 21,
            ),
            104 => 
            array (
                'id' => 107,
                'migration' => '2019_04_15_102108_alter1_my_task_table',
                'batch' => 22,
            ),
            105 => 
            array (
                'id' => 108,
                'migration' => '2019_04_15_103548_alter_vote_colnum_not_null_table',
                'batch' => 23,
            ),
            106 => 
            array (
                'id' => 109,
                'migration' => '2019_04_15_110733_alter1_task_table',
                'batch' => 24,
            ),
            107 => 
            array (
                'id' => 110,
                'migration' => '2019_03_19_130148_create_schedules_table',
                'batch' => 25,
            ),
            108 => 
            array (
                'id' => 111,
                'migration' => '2019_03_21_135907_create_user_schedules_table',
                'batch' => 25,
            ),
            109 => 
            array (
                'id' => 112,
                'migration' => '2019_04_15_154907_alter_contract_add_colnum_entry_id_table',
                'batch' => 26,
            ),
            110 => 
            array (
                'id' => 113,
                'migration' => '2019_04_15_160140_alter2_total_comment_table',
                'batch' => 26,
            ),
            111 => 
            array (
                'id' => 114,
                'migration' => '2019_04_15_160645_alter1_performance_template_table',
                'batch' => 26,
            ),
            112 => 
            array (
                'id' => 115,
                'migration' => '2019_04_15_162742_alter3_total_comment_table',
                'batch' => 26,
            ),
            113 => 
            array (
                'id' => 116,
                'migration' => '2019_04_15_165642_create_performance_template_son_table',
                'batch' => 26,
            ),
            114 => 
            array (
                'id' => 117,
                'migration' => '2019_04_15_172009_alter4_total_comment_table',
                'batch' => 26,
            ),
            115 => 
            array (
                'id' => 118,
                'migration' => '2019_04_15_180203_alter_vote_colnum_default_table',
                'batch' => 26,
            ),
            116 => 
            array (
                'id' => 119,
                'migration' => '2019_04_15_180204_create_performance_template_quota_table',
                'batch' => 26,
            ),
            117 => 
            array (
                'id' => 120,
                'migration' => '2019_04_15_185029_del_performance_application_table',
                'batch' => 26,
            ),
            118 => 
            array (
                'id' => 121,
                'migration' => '2019_04_15_185510_alter_performance_application_table',
                'batch' => 26,
            ),
            119 => 
            array (
                'id' => 122,
                'migration' => '2019_04_15_193245_create_performance_application_son_table',
                'batch' => 26,
            ),
            120 => 
            array (
                'id' => 123,
                'migration' => '2019_04_14_150645_add_classes_id_to_attendance_api_clock_table',
                'batch' => 27,
            ),
            121 => 
            array (
                'id' => 124,
                'migration' => '2019_04_16_095026_alter4_total_comment_table',
                'batch' => 27,
            ),
            122 => 
            array (
                'id' => 125,
                'migration' => '2019_04_16_095716_create_task_score_table',
                'batch' => 27,
            ),
            123 => 
            array (
                'id' => 126,
                'migration' => '2019_04_16_104354_alter_performance_template_quota_table',
                'batch' => 28,
            ),
            124 => 
            array (
                'id' => 127,
                'migration' => '2019_04_16_095026_alter4_total_add_comment_8910_table',
                'batch' => 29,
            ),
            125 => 
            array (
                'id' => 128,
                'migration' => '2019_04_16_172232_create_vote_department_table',
                'batch' => 29,
            ),
            126 => 
            array (
                'id' => 129,
                'migration' => '2019_04_16_144648_add_status_to_attendance_api_clock_table',
                'batch' => 30,
            ),
            127 => 
            array (
                'id' => 130,
                'migration' => '2019_04_16_152221_create_user_vacation_table',
                'batch' => 30,
            ),
            128 => 
            array (
                'id' => 131,
                'migration' => '2019_04_16_160414_alter2_performance_template_table',
                'batch' => 30,
            ),
            129 => 
            array (
                'id' => 132,
                'migration' => '2019_04_16_160447_add_overtime_date_type_to_attendance_api_anomaly_table',
                'batch' => 30,
            ),
            130 => 
            array (
                'id' => 133,
                'migration' => '2019_04_16_163414_alter4_performance_template_table',
                'batch' => 30,
            ),
            131 => 
            array (
                'id' => 134,
                'migration' => '2019_04_16_164414_alter3_performance_template_table',
                'batch' => 30,
            ),
            132 => 
            array (
                'id' => 135,
                'migration' => '2019_04_16_210012_alter5_total_comment_table',
                'batch' => 31,
            ),
            133 => 
            array (
                'id' => 136,
                'migration' => '2019_04_17_131456_alter_vote_record_add_colnum_avatar_table',
                'batch' => 32,
            ),
            134 => 
            array (
                'id' => 137,
                'migration' => '2019_04_17_102039_alter3_performance_application_table',
                'batch' => 33,
            ),
            135 => 
            array (
                'id' => 138,
                'migration' => '2019_04_17_103902_alter5_performance_template_table',
                'batch' => 33,
            ),
            136 => 
            array (
                'id' => 139,
                'migration' => '2019_04_17_155626_alter2_my_task_table',
                'batch' => 34,
            ),
            137 => 
            array (
                'id' => 140,
                'migration' => '2019_04_17_174005_create_describe_table',
                'batch' => 35,
            ),
            138 => 
            array (
                'id' => 141,
                'migration' => '2019_04_18_164845_alter_performance_application_son_table',
                'batch' => 35,
            ),
            139 => 
            array (
                'id' => 142,
                'migration' => '2019_04_18_161014_alter_table_schedules_confirm_yes_comment',
                'batch' => 36,
            ),
            140 => 
            array (
                'id' => 143,
                'migration' => '2019_04_18_170420_create_attendance_work_classes_table',
                'batch' => 37,
            ),
            141 => 
            array (
                'id' => 144,
                'migration' => '2019_04_19_110952_alter4_users_table',
                'batch' => 37,
            ),
            142 => 
            array (
                'id' => 145,
                'migration' => '2019_04_14_130054_alter5_users_table',
                'batch' => 38,
            ),
            143 => 
            array (
                'id' => 146,
                'migration' => '2019_04_18_171459_alter_contract_add_colnum_entries_id',
                'batch' => 39,
            ),
            144 => 
            array (
                'id' => 147,
                'migration' => '2019_04_19_133857_add_clock_id_to_attendance_api_anomaly_table',
                'batch' => 39,
            ),
            145 => 
            array (
                'id' => 148,
                'migration' => '2019_04_19_171618_alter_users_add_colnum',
                'batch' => 40,
            ),
            146 => 
            array (
                'id' => 149,
                'migration' => '2019_04_19_174415_create_vacation_table',
                'batch' => 40,
            ),
            147 => 
            array (
                'id' => 150,
                'migration' => '2019_04_22_163223_create_vacation_extra_table',
                'batch' => 41,
            ),
            148 => 
            array (
                'id' => 151,
                'migration' => '2019_04_22_165234_create_vacation_leave_record_table',
                'batch' => 42,
            ),
            149 => 
            array (
                'id' => 152,
                'migration' => '2019_04_22_164508_create_table_workflow_user_sync_quick_shot',
                'batch' => 43,
            ),
            150 => 
            array (
                'id' => 153,
                'migration' => '2019_04_22_170027_create_vacation_patch_record_table',
                'batch' => 44,
            ),
            151 => 
            array (
                'id' => 154,
                'migration' => '2019_04_22_160615_create_vacation_outside_record_table',
                'batch' => 45,
            ),
            152 => 
            array (
                'id' => 155,
                'migration' => '2019_04_22_165139_create_vacation_business_trip_record_table',
                'batch' => 45,
            ),
            153 => 
            array (
                'id' => 156,
                'migration' => '2019_04_22_165821_create_vacation_trip_record_table',
                'batch' => 45,
            ),
            154 => 
            array (
                'id' => 157,
                'migration' => '2019_04_23_161255_create_image_table',
                'batch' => 45,
            ),
            155 => 
            array (
                'id' => 158,
                'migration' => '2019_04_23_175019_alter_workflow_user_sync_comment',
                'batch' => 45,
            ),
            156 => 
            array (
                'id' => 159,
                'migration' => '2019_04_24_130610_create_meeting_room_table',
                'batch' => 46,
            ),
            157 => 
            array (
                'id' => 160,
                'migration' => '2019_04_24_162142_alter_contract_drop_company_name_table',
                'batch' => 47,
            ),
            158 => 
            array (
                'id' => 161,
                'migration' => '2019_04_25_100437_create_meeting_room_config_table',
                'batch' => 48,
            ),
            159 => 
            array (
                'id' => 162,
                'migration' => '2019_04_25_134210_create_company_seals_table',
                'batch' => 49,
            ),
            160 => 
            array (
                'id' => 164,
                'migration' => '2019_04_25_161935_create_meeting_table',
                'batch' => 50,
            ),
            161 => 
            array (
                'id' => 165,
                'migration' => '2019_04_25_144141_create_reports_table',
                'batch' => 51,
            ),
            162 => 
            array (
                'id' => 166,
                'migration' => '2019_04_25_144330_create_report_templates_table',
                'batch' => 51,
            ),
            163 => 
            array (
                'id' => 167,
                'migration' => '2019_04_25_144402_create_report_template_forms_table',
                'batch' => 51,
            ),
            164 => 
            array (
                'id' => 168,
                'migration' => '2019_04_25_144505_create_report_rules_table',
                'batch' => 51,
            ),
            165 => 
            array (
                'id' => 169,
                'migration' => '2019_04_25_144534_create_report_rule_users_table',
                'batch' => 51,
            ),
            166 => 
            array (
                'id' => 170,
                'migration' => '2019_04_25_153843_delete_table_work_report_receivers',
                'batch' => 51,
            ),
            167 => 
            array (
                'id' => 171,
                'migration' => '2019_04_25_153908_delete_table_work_report_rules',
                'batch' => 51,
            ),
            168 => 
            array (
                'id' => 172,
                'migration' => '2019_04_25_153926_delete_table_work_reports',
                'batch' => 51,
            ),
            169 => 
            array (
                'id' => 173,
                'migration' => '2019_04_26_103338_add_lng_lat_to_attendance_api_table',
                'batch' => 51,
            ),
            170 => 
            array (
                'id' => 174,
                'migration' => '2019_04_26_101942_create_meeting_participant_table',
                'batch' => 52,
            ),
            171 => 
            array (
                'id' => 175,
                'migration' => '2019_04_26_103652_create_meeting_task_table',
                'batch' => 52,
            ),
            172 => 
            array (
                'id' => 176,
                'migration' => '2019_04_26_133332_add_attendance_id_to_departments_table',
                'batch' => 53,
            ),
            173 => 
            array (
                'id' => 177,
                'migration' => '2019_04_26_142117_alter_users_detail_info_picture_type_table',
                'batch' => 54,
            ),
            174 => 
            array (
                'id' => 178,
                'migration' => '2019_04_26_144913_alter_meeting_table',
                'batch' => 55,
            ),
            175 => 
            array (
                'id' => 180,
                'migration' => '2019_04_26_145013_alter1_meeting_table',
                'batch' => 56,
            ),
            176 => 
            array (
                'id' => 181,
                'migration' => '2019_04_27_145013_alter2_meeting_table',
                'batch' => 57,
            ),
            177 => 
            array (
                'id' => 182,
                'migration' => '2019_04_26_135329_create_social_security_relation_table',
                'batch' => 58,
            ),
            178 => 
            array (
                'id' => 183,
                'migration' => '2019_04_28_104434_alter_table_workflow_template_form_add_column_length',
                'batch' => 59,
            ),
            179 => 
            array (
                'id' => 186,
                'migration' => '2019_04_28_112916_add_chinese_name_to_meeting_task_table',
                'batch' => 60,
            ),
            180 => 
            array (
                'id' => 187,
                'migration' => '2019_04_28_112940_add_chinese_name_to_meeting_participant_table',
                'batch' => 60,
            ),
            181 => 
            array (
                'id' => 188,
                'migration' => '2019_04_28_104142_create_administrative_contract_table',
                'batch' => 61,
            ),
            182 => 
            array (
                'id' => 189,
                'migration' => '2019_04_28_141926_create_company_seals_type_table',
                'batch' => 62,
            ),
            183 => 
            array (
                'id' => 190,
                'migration' => '2019_04_28_143411_alter_company_seals_table',
                'batch' => 62,
            ),
            184 => 
            array (
                'id' => 191,
                'migration' => '2019_04_28_161831_alter1_company_seals_table',
                'batch' => 63,
            ),
            185 => 
            array (
                'id' => 192,
                'migration' => '2019_04_28_170053_add_column_entries_id_to_workflow_user_sync_table',
                'batch' => 64,
            ),
            186 => 
            array (
                'id' => 193,
                'migration' => '2019_04_29_102050_add_uinque_to_workflow_user_sync_table',
                'batch' => 65,
            ),
            187 => 
            array (
                'id' => 194,
                'migration' => '2019_04_29_111454_add_deadline_repetition_time_to_meeting_table',
                'batch' => 66,
            ),
            188 => 
            array (
                'id' => 195,
                'migration' => '2019_04_29_103349_alter_total_comemnts_modify_column_add_const_table',
                'batch' => 67,
            ),
            189 => 
            array (
                'id' => 196,
                'migration' => '2019_04_29_110238_create_user_accounts_table',
                'batch' => 68,
            ),
            190 => 
            array (
                'id' => 197,
                'migration' => '2019_04_29_111610_create_user_account_profits_table',
                'batch' => 68,
            ),
            191 => 
            array (
                'id' => 198,
                'migration' => '2019_04_29_161208_create_document_table',
                'batch' => 69,
            ),
            192 => 
            array (
                'id' => 199,
                'migration' => '2019_04_29_163431_create_investments_table',
                'batch' => 69,
            ),
            193 => 
            array (
                'id' => 200,
                'migration' => '2019_04_29_172222_alter_feedback_content_add_column_attatchments_table',
                'batch' => 69,
            ),
            194 => 
            array (
                'id' => 201,
                'migration' => '2019_04_29_194915_alter6_total_comment_table',
                'batch' => 70,
            ),
            195 => 
            array (
                'id' => 203,
                'migration' => '2019_04_30_132605_add_summary_id_to_meeting_table',
                'batch' => 71,
            ),
            196 => 
            array (
                'id' => 204,
                'migration' => '2019_04_29_190409_create_profit_projects_table',
                'batch' => 72,
            ),
            197 => 
            array (
                'id' => 205,
                'migration' => '2019_04_30_131623_create_user_account_records_table',
                'batch' => 72,
            ),
            198 => 
            array (
                'id' => 206,
                'migration' => '2019_04_30_164734_create_attendance_records_table',
                'batch' => 72,
            ),
            199 => 
            array (
                'id' => 207,
                'migration' => '2019_05_05_092411_delete_table_report_rule_departments',
                'batch' => 73,
            ),
            200 => 
            array (
                'id' => 208,
                'migration' => '2019_05_05_092506_delete_table_report_rule_users',
                'batch' => 73,
            ),
            201 => 
            array (
                'id' => 209,
                'migration' => '2019_05_05_092539_delete_table_report_rules',
                'batch' => 73,
            ),
            202 => 
            array (
                'id' => 210,
                'migration' => '2019_05_05_092611_delete_table_report_template_forms',
                'batch' => 73,
            ),
            203 => 
            array (
                'id' => 211,
                'migration' => '2019_05_05_092635_delete_table_report_templates',
                'batch' => 73,
            ),
            204 => 
            array (
                'id' => 212,
                'migration' => '2019_05_05_092700_delete_table_reports',
                'batch' => 73,
            ),
            205 => 
            array (
                'id' => 213,
                'migration' => '2019_05_05_133138_add_clock_nums_to_attendance_api_anomaly_table',
                'batch' => 74,
            ),
            206 => 
            array (
                'id' => 214,
                'migration' => '2019_05_05_134740_create_financial_details_table',
                'batch' => 75,
            ),
            207 => 
            array (
                'id' => 215,
                'migration' => '2019_05_05_134749_create_financial_pics_table',
                'batch' => 75,
            ),
            208 => 
            array (
                'id' => 216,
                'migration' => '2019_05_05_141651_create_financial_table',
                'batch' => 75,
            ),
            209 => 
            array (
                'id' => 217,
                'migration' => '2019_05_06_092512_create_executive_cars_table',
                'batch' => 76,
            ),
            210 => 
            array (
                'id' => 218,
                'migration' => '2019_05_06_095626_create_pas_supplier_table',
                'batch' => 77,
            ),
            211 => 
            array (
                'id' => 219,
                'migration' => '2019_05_06_102945_create_pas_purchase_table',
                'batch' => 77,
            ),
            212 => 
            array (
                'id' => 220,
                'migration' => '2019_05_06_161351_create_pas_warehouse_table',
                'batch' => 77,
            ),
            213 => 
            array (
                'id' => 221,
                'migration' => '2019_05_06_152926_create_executive_cars_record_table',
                'batch' => 78,
            ),
            214 => 
            array (
                'id' => 222,
                'migration' => '2019_05_06_165757_create_pas_commodity_category_table',
                'batch' => 78,
            ),
            215 => 
            array (
                'id' => 223,
                'migration' => '2019_05_06_171112_create_reports_table',
                'batch' => 78,
            ),
            216 => 
            array (
                'id' => 224,
                'migration' => '2019_05_06_171200_create_report_templates_table',
                'batch' => 78,
            ),
            217 => 
            array (
                'id' => 225,
                'migration' => '2019_05_06_171345_create_report_template_forms_table',
                'batch' => 78,
            ),
            218 => 
            array (
                'id' => 226,
                'migration' => '2019_05_06_171415_create_report_rules_table',
                'batch' => 78,
            ),
            219 => 
            array (
                'id' => 227,
                'migration' => '2019_05_07_091331_create_supervises_table',
                'batch' => 79,
            ),
            220 => 
            array (
                'id' => 228,
                'migration' => '2019_05_07_091941_create_attentions_table',
                'batch' => 79,
            ),
            221 => 
            array (
                'id' => 229,
                'migration' => '2019_05_07_094634_alter_company_vacation_add_column_discount_of_salary_table',
                'batch' => 79,
            ),
            222 => 
            array (
                'id' => 231,
                'migration' => '2019_05_07_110613_create_goods_allocation_in_table',
                'batch' => 80,
            ),
            223 => 
            array (
                'id' => 232,
                'migration' => '2019_05_07_112153_create_warehouse_goods_location_table',
                'batch' => 80,
            ),
            224 => 
            array (
                'id' => 233,
                'migration' => '2019_05_07_114233_add_column_salary_version_to_contract_table',
                'batch' => 80,
            ),
            225 => 
            array (
                'id' => 234,
                'migration' => '2019_05_07_130820_create_users_salary_table',
                'batch' => 80,
            ),
            226 => 
            array (
                'id' => 235,
                'migration' => '2019_05_07_114116_create_goods_allocation_goods_table',
                'batch' => 81,
            ),
            227 => 
            array (
                'id' => 236,
                'migration' => '2019_05_07_115756_create_warehouse_out_card_table',
                'batch' => 81,
            ),
            228 => 
            array (
                'id' => 237,
                'migration' => '2019_05_06_180419_create_pas_cost_information_table',
                'batch' => 82,
            ),
            229 => 
            array (
                'id' => 238,
                'migration' => '2019_05_07_102234_create_pas_warehousing_apply_table',
                'batch' => 82,
            ),
            230 => 
            array (
                'id' => 239,
                'migration' => '2019_05_07_110842_create_pas_return_order_table',
                'batch' => 82,
            ),
            231 => 
            array (
                'id' => 240,
                'migration' => '2019_05_07_132316_create_pas_purchase_commodity_table',
                'batch' => 82,
            ),
            232 => 
            array (
                'id' => 241,
                'migration' => '2019_05_07_164120_create_pas_warehouse_delivery_type_table',
                'batch' => 83,
            ),
            233 => 
            array (
                'id' => 242,
                'migration' => '2019_05_07_165337_create_logistics_table',
                'batch' => 83,
            ),
            234 => 
            array (
                'id' => 243,
                'migration' => '2019_05_07_165724_create_pas_logistics_point_table',
                'batch' => 83,
            ),
            235 => 
            array (
                'id' => 244,
                'migration' => '2019_05_07_170028_create_warehouse_out_goods_table',
                'batch' => 83,
            ),
            236 => 
            array (
                'id' => 245,
                'migration' => '2019_05_07_172054_create_pas_purchase_commodity_content_table',
                'batch' => 83,
            ),
            237 => 
            array (
                'id' => 246,
                'migration' => '2019_05_07_173005_create_allot_cart_table',
                'batch' => 83,
            ),
            238 => 
            array (
                'id' => 247,
                'migration' => '2019_05_07_173014_add_apply_id_to_pas_purchase_table',
                'batch' => 83,
            ),
            239 => 
            array (
                'id' => 248,
                'migration' => '2019_05_07_174157_create_allot_card_goods_table',
                'batch' => 83,
            ),
            240 => 
            array (
                'id' => 249,
                'migration' => '2019_05_07_180150_create_pas_stock_check_table',
                'batch' => 83,
            ),
            241 => 
            array (
                'id' => 250,
                'migration' => '2019_05_07_181345_create_pas_stock_check_goods_table',
                'batch' => 83,
            ),
            242 => 
            array (
                'id' => 251,
                'migration' => '2019_05_08_133830_add_ctype_to_pas_supplier_table',
                'batch' => 84,
            ),
            243 => 
            array (
                'id' => 252,
                'migration' => '2019_01_01_105839_init-oa-tables-struct',
                'batch' => 85,
            ),
            244 => 
            array (
                'id' => 253,
                'migration' => '2019_05_07_112749_entry_type_table',
                'batch' => 85,
            ),
            245 => 
            array (
                'id' => 254,
                'migration' => '2019_05_08_135509_create_basic_oa_option_table',
                'batch' => 86,
            ),
            246 => 
            array (
                'id' => 255,
                'migration' => '2019_05_08_135601_create_basic_oa_type_table',
                'batch' => 87,
            ),
            247 => 
            array (
                'id' => 257,
                'migration' => '2019_05_08_170158_add_remark_to_executive_cars_table',
                'batch' => 88,
            ),
            248 => 
            array (
                'id' => 258,
                'migration' => '2019_05_08_172752_add_entrise_id_to_pas_purchase_table',
                'batch' => 89,
            ),
            249 => 
            array (
                'id' => 259,
                'migration' => '2019_05_08_172757_create_executive_cars_use_table',
                'batch' => 90,
            ),
            250 => 
            array (
                'id' => 260,
                'migration' => '2019_05_08_100123_create_corporate_assets_table',
                'batch' => 91,
            ),
            251 => 
            array (
                'id' => 261,
                'migration' => '2019_05_08_145637_alter_column_to_contract_table',
                'batch' => 91,
            ),
            252 => 
            array (
                'id' => 262,
                'migration' => '2019_05_08_150018_create_corporate_assets_sync_table',
                'batch' => 91,
            ),
            253 => 
            array (
                'id' => 263,
                'migration' => '2019_05_08_152437_add_column_status_to_corporate_assets_table',
                'batch' => 91,
            ),
            254 => 
            array (
                'id' => 264,
                'migration' => '2019_05_08_153339_create_corporate_assets_use_table',
                'batch' => 91,
            ),
            255 => 
            array (
                'id' => 265,
                'migration' => '2019_05_08_160023_create_corporate_assets_borrow_table',
                'batch' => 91,
            ),
            256 => 
            array (
                'id' => 266,
                'migration' => '2019_05_08_160158_create_corporate_assets_return_table',
                'batch' => 91,
            ),
            257 => 
            array (
                'id' => 267,
                'migration' => '2019_05_08_160240_create_corporate_assets_transfer_table',
                'batch' => 91,
            ),
            258 => 
            array (
                'id' => 268,
                'migration' => '2019_05_08_160320_create_corporate_assets_repair_table',
                'batch' => 91,
            ),
            259 => 
            array (
                'id' => 269,
                'migration' => '2019_05_08_160349_create_corporate_assets_scrapped_table',
                'batch' => 91,
            ),
            260 => 
            array (
                'id' => 270,
                'migration' => '2019_05_08_160937_create_corporate_assets_valueadded_table',
                'batch' => 91,
            ),
            261 => 
            array (
                'id' => 271,
                'migration' => '2019_05_08_161045_create_corporate_assets_depreciation_table',
                'batch' => 91,
            ),
            262 => 
            array (
                'id' => 272,
                'migration' => '2019_05_08_173804_create_corporate_assets_relation_table',
                'batch' => 91,
            ),
            263 => 
            array (
                'id' => 273,
                'migration' => '2019_05_09_094149_add_supplier_name_to_pas_purchase_table',
                'batch' => 92,
            ),
            264 => 
            array (
                'id' => 274,
                'migration' => '2019_05_08_181029_create_pas_warehouse_in_goods_table',
                'batch' => 93,
            ),
            265 => 
            array (
                'id' => 275,
                'migration' => '2019_05_09_151116_create_pas_goods_table',
                'batch' => 93,
            ),
            266 => 
            array (
                'id' => 276,
                'migration' => '2019_05_09_151146_create_pas_goods_attributes_table',
                'batch' => 93,
            ),
            267 => 
            array (
                'id' => 277,
                'migration' => '2019_05_09_151221_create_pas_goods_specific_prices_table',
                'batch' => 93,
            ),
            268 => 
            array (
                'id' => 278,
                'migration' => '2019_05_09_151250_create_pas_goods_specifics_table',
                'batch' => 93,
            ),
            269 => 
            array (
                'id' => 279,
                'migration' => '2019_05_09_151319_create_pas_specific_items_table',
                'batch' => 93,
            ),
            270 => 
            array (
                'id' => 280,
                'migration' => '2019_05_09_151350_create_pas_specifics_table',
                'batch' => 93,
            ),
            271 => 
            array (
                'id' => 281,
                'migration' => '2019_05_09_151414_create_pas_attributes_table',
                'batch' => 93,
            ),
            272 => 
            array (
                'id' => 282,
                'migration' => '2019_05_09_151438_create_pas_categorys_table',
                'batch' => 93,
            ),
            273 => 
            array (
                'id' => 283,
                'migration' => '2019_05_09_133838_add_user_id_to_pas_warehousing_apply_table',
                'batch' => 94,
            ),
            274 => 
            array (
                'id' => 284,
                'migration' => '2019_05_09_173058_add_goods_id_to_pas_purchase_commodity_content_table',
                'batch' => 94,
            ),
            275 => 
            array (
                'id' => 285,
                'migration' => '2019_05_09_142659_create_welfares_table',
                'batch' => 95,
            ),
            276 => 
            array (
                'id' => 286,
                'migration' => '2019_05_09_142737_create_welfare_receivers_table',
                'batch' => 95,
            ),
            277 => 
            array (
                'id' => 287,
                'migration' => '2019_05_09_113456_add_column_department_id_to_corporate_assets_table',
                'batch' => 96,
            ),
            278 => 
            array (
                'id' => 288,
                'migration' => '2019_05_09_151455_create_executive_cars_appoint_table',
                'batch' => 96,
            ),
            279 => 
            array (
                'id' => 289,
                'migration' => '2019_05_09_175508_create_executive_cars_sendback_table',
                'batch' => 96,
            ),
            280 => 
            array (
                'id' => 290,
                'migration' => '2019_05_08_173427_alter_warehouse_in_card_modify_applyid_column',
                'batch' => 97,
            ),
            281 => 
            array (
                'id' => 291,
                'migration' => '2019_05_09_102249_alter_goods_allocation_goods_add_sku_id_cloumn',
                'batch' => 97,
            ),
            282 => 
            array (
                'id' => 292,
                'migration' => '2019_05_09_153650_alter_pas_warehouse_out_card_add_apply_id_column',
                'batch' => 97,
            ),
            283 => 
            array (
                'id' => 293,
                'migration' => '2019_05_09_175616_alter_pas_warehouse_delivery_type_add_user_id_colomn',
                'batch' => 97,
            ),
            284 => 
            array (
                'id' => 294,
                'migration' => '2019_05_10_095156_add_column_entry_id_to_corporate_assets_relation_table',
                'batch' => 97,
            ),
            285 => 
            array (
                'id' => 295,
                'migration' => '2019_05_10_095604_alter_add_column_logistics_id_for_pas_logistics_point_table',
                'batch' => 97,
            ),
            286 => 
            array (
                'id' => 296,
                'migration' => '2019_05_10_125910_alert_reports_table',
                'batch' => 98,
            ),
            287 => 
            array (
                'id' => 297,
                'migration' => '2019_05_10_130900_delete_table_reports',
                'batch' => 99,
            ),
            288 => 
            array (
                'id' => 298,
                'migration' => '2019_05_10_131243_create_reports_table',
                'batch' => 100,
            ),
            289 => 
            array (
                'id' => 299,
                'migration' => '2019_05_10_131928_create_likes_table',
                'batch' => 101,
            ),
            290 => 
            array (
                'id' => 301,
                'migration' => '2019_05_10_160542_add_goods_url_to_pas_purchase_commodity_content_table',
                'batch' => 102,
            ),
            291 => 
            array (
                'id' => 304,
                'migration' => '2019_05_10_160237_create_pas_warehousing_apply_com_table',
                'batch' => 103,
            ),
            292 => 
            array (
                'id' => 305,
                'migration' => '2019_05_09_112406_create_intelligence_table',
                'batch' => 104,
            ),
            293 => 
            array (
                'id' => 306,
                'migration' => '2019_05_09_112935_create_intelligence_type_table',
                'batch' => 104,
            ),
            294 => 
            array (
                'id' => 307,
                'migration' => '2019_05_10_103036_add_column_use_at_to_corporate_assets_use_table',
                'batch' => 104,
            ),
            295 => 
            array (
                'id' => 308,
                'migration' => '2019_05_10_112150_create_intelligence_info_table',
                'batch' => 104,
            ),
            296 => 
            array (
                'id' => 309,
                'migration' => '2019_05_10_112331_create_intelligence_users_table',
                'batch' => 105,
            ),
            297 => 
            array (
                'id' => 310,
                'migration' => '2019_05_09_175654_delete_table_reports',
                'batch' => 106,
            ),
            298 => 
            array (
                'id' => 311,
                'migration' => '2019_05_10_180325_create_reports_table',
                'batch' => 107,
            ),
            299 => 
            array (
                'id' => 313,
                'migration' => '2019_05_11_095047_add_goods_name_to_pas_warehousing_apply_table',
                'batch' => 108,
            ),
            300 => 
            array (
                'id' => 314,
                'migration' => '2019_05_11_114429_add_sku_id_to_pas_purchase_commodity_content_table',
                'batch' => 109,
            ),
            301 => 
            array (
                'id' => 315,
                'migration' => '2019_05_11_141132_add_email_to_pas_supplier_table',
                'batch' => 110,
            ),
            302 => 
            array (
                'id' => 316,
                'migration' => '2019_05_13_092940_add_user_id_to_pas_return_order_table',
                'batch' => 111,
            ),
            303 => 
            array (
                'id' => 317,
                'migration' => '2019_05_13_131702_add_supplier_name_to_pas_return_order_table',
                'batch' => 112,
            ),
            304 => 
            array (
                'id' => 318,
                'migration' => '2019_05_13_101420_create_salary_reward_punishment_table',
                'batch' => 113,
            ),
            305 => 
            array (
                'id' => 319,
                'migration' => '2019_05_13_144629_create_salary_reward_punishment_complain_table',
                'batch' => 113,
            ),
            306 => 
            array (
                'id' => 320,
                'migration' => '2019_05_13_155838_delete_table_pas_attributes',
                'batch' => 114,
            ),
            307 => 
            array (
                'id' => 321,
                'migration' => '2019_05_13_155914_delete_table_pas_categorys',
                'batch' => 114,
            ),
            308 => 
            array (
                'id' => 322,
                'migration' => '2019_05_13_155945_delete_table_pas_goods',
                'batch' => 114,
            ),
            309 => 
            array (
                'id' => 323,
                'migration' => '2019_05_13_160020_delete_table_pas_goods_attributes',
                'batch' => 114,
            ),
            310 => 
            array (
                'id' => 324,
                'migration' => '2019_05_13_160101_delete_table_pas_goods_specific_prices',
                'batch' => 114,
            ),
            311 => 
            array (
                'id' => 325,
                'migration' => '2019_05_13_160132_delete_table_pas_goods_specifics',
                'batch' => 114,
            ),
            312 => 
            array (
                'id' => 326,
                'migration' => '2019_05_13_160317_delete_table_pas_specifics',
                'batch' => 114,
            ),
            313 => 
            array (
                'id' => 327,
                'migration' => '2019_05_13_160343_delete_table_pas_specific_items',
                'batch' => 114,
            ),
            314 => 
            array (
                'id' => 328,
                'migration' => '2019_05_13_160501_create_pas_attributes_table',
                'batch' => 114,
            ),
            315 => 
            array (
                'id' => 329,
                'migration' => '2019_05_13_160537_create_pas_categorys_table',
                'batch' => 114,
            ),
            316 => 
            array (
                'id' => 330,
                'migration' => '2019_05_13_160603_create_pas_goods_table',
                'batch' => 114,
            ),
            317 => 
            array (
                'id' => 331,
                'migration' => '2019_05_13_160631_create_pas_goods_attributes_table',
                'batch' => 114,
            ),
            318 => 
            array (
                'id' => 332,
                'migration' => '2019_05_13_160700_create_pas_goods_specific_prices_table',
                'batch' => 114,
            ),
            319 => 
            array (
                'id' => 333,
                'migration' => '2019_05_13_160728_create_pas_goods_specifics_table',
                'batch' => 114,
            ),
            320 => 
            array (
                'id' => 334,
                'migration' => '2019_05_13_160753_create_pas_specific_items_table',
                'batch' => 114,
            ),
            321 => 
            array (
                'id' => 335,
                'migration' => '2019_05_13_160817_create_pas_specifics_table',
                'batch' => 114,
            ),
            322 => 
            array (
                'id' => 336,
                'migration' => '2019_05_13_161946_create_pas_brands_table',
                'batch' => 114,
            ),
            323 => 
            array (
                'id' => 337,
                'migration' => '2019_05_13_162141_create_pas_sale_order_goods_table',
                'batch' => 114,
            ),
            324 => 
            array (
                'id' => 338,
                'migration' => '2019_05_07_103912_create_goods_allocation_table',
                'batch' => 115,
            ),
            325 => 
            array (
                'id' => 339,
                'migration' => '2019_05_08_152213_alter_pas_goods_allocation_add_cloumn_no_table',
                'batch' => 115,
            ),
            326 => 
            array (
                'id' => 340,
                'migration' => '2019_05_13_165032_create_attendance_api_hr_update_clock_log_table',
                'batch' => 116,
            ),
            327 => 
            array (
                'id' => 341,
                'migration' => '2019_05_13_192134_create_pas_sale_invoices_table',
                'batch' => 117,
            ),
            328 => 
            array (
                'id' => 342,
                'migration' => '2019_05_13_192215_create_pas_sale_orders_table',
                'batch' => 117,
            ),
            329 => 
            array (
                'id' => 343,
                'migration' => '2019_05_13_155944_alter_assets_table',
                'batch' => 118,
            ),
            330 => 
            array (
                'id' => 344,
                'migration' => '2019_05_13_194512_add_column_repair_at_to_corporate_assets_repair_table',
                'batch' => 118,
            ),
            331 => 
            array (
                'id' => 345,
                'migration' => '2019_05_14_114103_create_pas_payment_order_table',
                'batch' => 118,
            ),
            332 => 
            array (
                'id' => 346,
                'migration' => '2019_05_14_140543_add_p_status_to_pas_purchase_table',
                'batch' => 119,
            ),
            333 => 
            array (
                'id' => 347,
                'migration' => '2019_05_08_171211_salary_social_security',
                'batch' => 120,
            ),
            334 => 
            array (
                'id' => 348,
                'migration' => '2019_05_08_171247_salary_attendance',
                'batch' => 120,
            ),
            335 => 
            array (
                'id' => 349,
                'migration' => '2019_05_09_094758_salary_form',
                'batch' => 120,
            ),
            336 => 
            array (
                'id' => 350,
                'migration' => '2019_05_09_112036_salary_record',
                'batch' => 120,
            ),
            337 => 
            array (
                'id' => 351,
                'migration' => '2019_05_10_165710_salary_record_sync_type',
                'batch' => 120,
            ),
            338 => 
            array (
                'id' => 352,
                'migration' => '2019_05_14_141031_add_delete_at_to_task_score_table',
                'batch' => 120,
            ),
            339 => 
            array (
                'id' => 353,
                'migration' => '2019_05_14_134632_create_table_position',
                'batch' => 121,
            ),
            340 => 
            array (
                'id' => 354,
                'migration' => '2019_05_14_134704_create_table_position_relate_department',
                'batch' => 121,
            ),
            341 => 
            array (
                'id' => 355,
                'migration' => '2019_05_14_131434_create_report_complain_table',
                'batch' => 122,
            ),
            342 => 
            array (
                'id' => 356,
                'migration' => '2019_05_15_110105_alter_goods_allocation_goods_add_warehouse_id_column',
                'batch' => 122,
            ),
            343 => 
            array (
                'id' => 357,
                'migration' => '2019_05_15_162721_alter_stock_check_add_status_column',
                'batch' => 123,
            ),
            344 => 
            array (
                'id' => 358,
                'migration' => '2019_05_16_131340_add_enclosure_img_to_task_table',
                'batch' => 123,
            ),
            345 => 
            array (
                'id' => 359,
                'migration' => '2019_05_16_132213_add_start_time_to_task_table',
                'batch' => 123,
            ),
            346 => 
            array (
                'id' => 360,
                'migration' => '2019_05_15_094532_add_column_to_corporate_assets_table',
                'batch' => 124,
            ),
            347 => 
            array (
                'id' => 361,
                'migration' => '2019_05_16_113419_alter_table_add_column_finish_at_workflow_procs',
                'batch' => 124,
            ),
            348 => 
            array (
                'id' => 362,
                'migration' => '2019_05_15_193254_create_goods_flow_table',
                'batch' => 125,
            ),
            349 => 
            array (
                'id' => 363,
                'migration' => '2019_05_16_153544_alter_message_add_readstatus_clomn',
                'batch' => 125,
            ),
            350 => 
            array (
                'id' => 364,
                'migration' => '2019_05_16_155421_create_pas_purchase_payable_money_table',
                'batch' => 125,
            ),
            351 => 
            array (
                'id' => 365,
                'migration' => '2019_05_17_090944_delete_table_pas_sale_orders',
                'batch' => 125,
            ),
            352 => 
            array (
                'id' => 366,
                'migration' => '2019_05_17_091019_delete_table_pas_sale_order_goods',
                'batch' => 125,
            ),
            353 => 
            array (
                'id' => 367,
                'migration' => '2019_05_17_091154_create_pas_sale_order_goods_tables',
                'batch' => 125,
            ),
            354 => 
            array (
                'id' => 368,
                'migration' => '2019_05_17_091217_create_pas_sale_orders_tables',
                'batch' => 125,
            ),
            355 => 
            array (
                'id' => 369,
                'migration' => '2019_05_17_091304_create_pas_sale_out_warehouse_tables',
                'batch' => 125,
            ),
            356 => 
            array (
                'id' => 370,
                'migration' => '2019_05_17_091330_create_pas_sale_out_warehouse_goods_tables',
                'batch' => 125,
            ),
            357 => 
            array (
                'id' => 371,
                'migration' => '2019_05_17_102453_alter_companies_add_tel_table',
                'batch' => 125,
            ),
            358 => 
            array (
                'id' => 372,
                'migration' => '2019_05_17_135716_add_column_to_corporate_assets_relation_table',
                'batch' => 126,
            ),
            359 => 
            array (
                'id' => 373,
                'migration' => '2019_05_18_103024_add_rw_number_to_pas_purchase_commodity_content_table',
                'batch' => 126,
            ),
            360 => 
            array (
                'id' => 374,
                'migration' => '2019_05_18_113346_alert_start_end_to_meeting_table',
                'batch' => 126,
            ),
            361 => 
            array (
                'id' => 375,
                'migration' => '2019_05_20_095443_create_corporate_assets_innerdb_table',
                'batch' => 127,
            ),
            362 => 
            array (
                'id' => 376,
                'migration' => '2019_05_20_100158_create_pas_sale_return_in_warehouse_table',
                'batch' => 127,
            ),
            363 => 
            array (
                'id' => 377,
                'migration' => '2019_05_20_100226_create_pas_sale_return_in_warehouse_goods_table',
                'batch' => 127,
            ),
            364 => 
            array (
                'id' => 378,
                'migration' => '2019_05_20_100452_create_growth_recode_table',
                'batch' => 127,
            ),
            365 => 
            array (
                'id' => 379,
                'migration' => '2019_05_17_171936_alter_table_salary_form_add_column_human_cost',
                'batch' => 128,
            ),
            366 => 
            array (
                'id' => 380,
                'migration' => '2019_05_20_161455_alter_table_salary_record_add_column_entry_id',
                'batch' => 128,
            ),
            367 => 
            array (
                'id' => 381,
                'migration' => '2019_05_21_095557_add_task_id_to_salary_reward_punishment_table',
                'batch' => 128,
            ),
            368 => 
            array (
                'id' => 382,
                'migration' => '2019_05_21_111241_add_title_to_attendance_api_overtime_rule_table',
                'batch' => 128,
            ),
            369 => 
            array (
                'id' => 383,
                'migration' => '2019_05_21_161004_alter_financial_table',
                'batch' => 128,
            ),
            370 => 
            array (
                'id' => 384,
                'migration' => '2019_05_20_132740_alter_workflow_flows_table',
                'batch' => 128,
            ),
            371 => 
            array (
                'id' => 385,
                'migration' => '2019_05_20_133353_alter_workflow_flow_types_table',
                'batch' => 128,
            ),
            372 => 
            array (
                'id' => 386,
                'migration' => '2019_05_21_153654_add_user_id_to_task_score_table',
                'batch' => 129,
            ),
            373 => 
            array (
                'id' => 387,
                'migration' => '2019_05_21_160642_alter_column_number_to_vote_table',
                'batch' => 129,
            ),
            374 => 
            array (
                'id' => 388,
                'migration' => '2019_05_21_171611_alter_financial_table',
                'batch' => 129,
            ),
            375 => 
            array (
                'id' => 389,
                'migration' => '2019_05_21_154505_create_transaction_logs_table',
                'batch' => 130,
            ),
            376 => 
            array (
                'id' => 390,
                'migration' => '2019_05_21_165447_create_task_score_log_by_month_table',
                'batch' => 130,
            ),
            377 => 
            array (
                'id' => 391,
                'migration' => '2019_05_21_171034_alert_pas_brands_table',
                'batch' => 130,
            ),
            378 => 
            array (
                'id' => 392,
                'migration' => '2019_05_21_173652_add_deleted_at_to_feedback_content_table',
                'batch' => 130,
            ),
            379 => 
            array (
                'id' => 393,
                'migration' => '2019_05_22_095521_add_column_to_message_table',
                'batch' => 130,
            ),
            380 => 
            array (
                'id' => 394,
                'migration' => '2019_05_22_110153_add_column_avatar_to_vote_participant_table',
                'batch' => 130,
            ),
            381 => 
            array (
                'id' => 395,
                'migration' => '2019_05_22_165529_create_financial_log_table',
                'batch' => 131,
            ),
            382 => 
            array (
                'id' => 396,
                'migration' => '2019_05_22_133114_add_type_to_punishment_template_table',
                'batch' => 132,
            ),
            383 => 
            array (
                'id' => 397,
                'migration' => '2019_05_22_151820_add_p_id_to_pas_warehousing_apply_table',
                'batch' => 132,
            ),
            384 => 
            array (
                'id' => 398,
                'migration' => '2019_05_22_152224_add_p_id_to_pas_payment_order_table',
                'batch' => 132,
            ),
            385 => 
            array (
                'id' => 399,
                'migration' => '2019_05_22_152305_add_p_id_to_pas_return_order_table',
                'batch' => 132,
            ),
            386 => 
            array (
                'id' => 400,
                'migration' => '2019_05_23_110248_add_column_to_corporate_assets_sync_table',
                'batch' => 132,
            ),
            387 => 
            array (
                'id' => 401,
                'migration' => '2019_05_23_133526_create_pas_payment_order_content_table',
                'batch' => 132,
            ),
            388 => 
            array (
                'id' => 402,
                'migration' => '2019_05_23_153746_add_p_status_to_pas_return_order_table',
                'batch' => 133,
            ),
            389 => 
            array (
                'id' => 403,
                'migration' => '2019_05_24_094050_add_my_task_id_and_admin_id_to_task_score_table',
                'batch' => 133,
            ),
            390 => 
            array (
                'id' => 404,
                'migration' => '2019_05_24_100718_alter_index_to_corporate_assets_relation_table',
                'batch' => 133,
            ),
            391 => 
            array (
                'id' => 405,
                'migration' => '2019_05_24_105745_alert_pas_sale_order_goods_table',
                'batch' => 133,
            ),
            392 => 
            array (
                'id' => 406,
                'migration' => '2019_05_24_111426_add_show_route_url_to_workflow_flows_table',
                'batch' => 133,
            ),
            393 => 
            array (
                'id' => 407,
                'migration' => '2019_05_24_145106_alter_financial_table',
                'batch' => 133,
            ),
            394 => 
            array (
                'id' => 408,
                'migration' => '2019_05_24_160459_alter_administrative_contract_entry_id_update_client_id_table',
                'batch' => 134,
            ),
            395 => 
            array (
                'id' => 409,
                'migration' => '2019_05_21_100541_alter_table_salary_record_sync_type_add_count',
                'batch' => 135,
            ),
            396 => 
            array (
                'id' => 410,
                'migration' => '2019_05_21_111345_alter_table_salary_form_add_column_status_view_withdraw',
                'batch' => 135,
            ),
            397 => 
            array (
                'id' => 411,
                'migration' => '2019_05_24_103320_alter_pas_warehouse_in_card_add_column_warehouse_id',
                'batch' => 135,
            ),
            398 => 
            array (
                'id' => 412,
                'migration' => '2019_05_24_145131_alter_pas_warehouse_in_goods_add_column_warehouse_id',
                'batch' => 135,
            ),
            399 => 
            array (
                'id' => 413,
                'migration' => '2019_05_24_180151_alter_pas_warehousing_apply_content_add_column_house_id',
                'batch' => 135,
            ),
            400 => 
            array (
                'id' => 414,
                'migration' => '2019_05_25_103312_create_financial_customer_table',
                'batch' => 135,
            ),
            401 => 
            array (
                'id' => 415,
                'migration' => '2019_05_25_105135_create_financial_order_table',
                'batch' => 135,
            ),
            402 => 
            array (
                'id' => 416,
                'migration' => '2019_05_25_144844_alter_message_change_column_type',
                'batch' => 136,
            ),
            403 => 
            array (
                'id' => 417,
                'migration' => '2019_05_25_151709_create_pas_sale_return_orders_table',
                'batch' => 136,
            ),
            404 => 
            array (
                'id' => 418,
                'migration' => '2019_05_25_151743_create_pas_sale_return_order_goods_table',
                'batch' => 136,
            ),
            405 => 
            array (
                'id' => 419,
                'migration' => '2019_05_25_173128_alter_pas_allot_card_goods_add_column_warehouse_id',
                'batch' => 136,
            ),
            406 => 
            array (
                'id' => 420,
                'migration' => '2019_05_25_173413_alter_pas_sale_return_in_warehouse_goods_add_column_warehouse_id',
                'batch' => 136,
            ),
            407 => 
            array (
                'id' => 421,
                'migration' => '2019_05_26_143044_alter_transaction_logs_add_source',
                'batch' => 136,
            ),
            408 => 
            array (
                'id' => 422,
                'migration' => '2019_05_27_092656_alter_users_add_index_unique',
                'batch' => 137,
            ),
            409 => 
            array (
                'id' => 423,
                'migration' => '2019_05_28_092729_alter_transaction_logs_table',
                'batch' => 138,
            ),
            410 => 
            array (
                'id' => 424,
                'migration' => '2019_05_28_094704_alter_financial_add_end_period_at',
                'batch' => 138,
            ),
            411 => 
            array (
                'id' => 425,
                'migration' => '2019_05_25_162730_alter_table_salary_form_add_column_performance',
                'batch' => 139,
            ),
            412 => 
            array (
                'id' => 426,
                'migration' => '2019_05_27_174603_alert_pas_sale_out_warehouse_table',
                'batch' => 139,
            ),
            413 => 
            array (
                'id' => 427,
                'migration' => '2019_05_27_174641_alert_pas_sale_out_warehouse_goods_table',
                'batch' => 139,
            ),
            414 => 
            array (
                'id' => 428,
                'migration' => '2019_05_27_174749_alert_pas_sale_return_orders_table',
                'batch' => 139,
            ),
            415 => 
            array (
                'id' => 429,
                'migration' => '2019_05_27_174848_alert_pas_sale_return_order_goods_table',
                'batch' => 139,
            ),
            416 => 
            array (
                'id' => 430,
                'migration' => '2019_05_27_175008_del_table_pas_sale_return_in_warehouse',
                'batch' => 139,
            ),
            417 => 
            array (
                'id' => 431,
                'migration' => '2019_05_27_175036_del_table_pas_sale_return_in_warehouse_goods',
                'batch' => 139,
            ),
            418 => 
            array (
                'id' => 432,
                'migration' => '2019_05_27_183325_creat_pas_sale_return_in_warehouse_table',
                'batch' => 139,
            ),
            419 => 
            array (
                'id' => 433,
                'migration' => '2019_05_27_183403_creat_pas_sale_return_in_warehouse_goods_table',
                'batch' => 139,
            ),
            420 => 
            array (
                'id' => 434,
                'migration' => '2019_05_28_133933_alert_pas_goods_add_back_num_table',
                'batch' => 139,
            ),
            421 => 
            array (
                'id' => 438,
                'migration' => '2019_05_28_141136_alter_table_user_schedules_add_column_prompt_type',
                'batch' => 139,
            ),
            422 => 
            array (
                'id' => 439,
                'migration' => '2019_05_28_150454_add_invoice_id_to_pas_return_order_table',
                'batch' => 140,
            ),
            423 => 
            array (
                'id' => 440,
                'migration' => '2019_05_28_173720_create_table_api_positions_roles',
                'batch' => 141,
            ),
            424 => 
            array (
                'id' => 441,
                'migration' => '2019_05_28_174835_add_sku_id_to_pas_warehousing_apply_content_table',
                'batch' => 141,
            ),
            425 => 
            array (
                'id' => 442,
                'migration' => '2019_05_28_175707_alter_table_workflow_entries_add_column_order_no',
                'batch' => 141,
            ),
            426 => 
            array (
                'id' => 443,
                'migration' => '2019_05_29_090929_alert_table_pas_sale_out_warehouse_goods',
                'batch' => 141,
            ),
            427 => 
            array (
                'id' => 444,
                'migration' => '2019_05_29_102257_add_clock_address_to_attendance_api_clock_table',
                'batch' => 141,
            ),
            428 => 
            array (
                'id' => 445,
                'migration' => '2019_05_29_115103_alert_table_report_templates',
                'batch' => 141,
            ),
            429 => 
            array (
                'id' => 446,
                'migration' => '2019_05_29_115129_alert_table_report_template_forms',
                'batch' => 141,
            ),
            430 => 
            array (
                'id' => 447,
                'migration' => '2019_05_29_115149_alter_pas_warehouse_in_goods_add_clumn_aplly_id',
                'batch' => 141,
            ),
            431 => 
            array (
                'id' => 448,
                'migration' => '2019_05_29_115650_alert_table_pas_sale_return_in_warehouse_goods',
                'batch' => 141,
            ),
            432 => 
            array (
                'id' => 449,
                'migration' => '2019_05_29_141754_add_invoice_id_to_pas_warehousing_apply_table',
                'batch' => 142,
            ),
            433 => 
            array (
                'id' => 450,
                'migration' => '2019_05_29_152841_add_entrise_id_to_meeting_room_table',
                'batch' => 142,
            ),
            434 => 
            array (
                'id' => 451,
                'migration' => '2019_05_29_170549_alter_pas_goods_flow_add_column_allocation_id',
                'batch' => 142,
            ),
            435 => 
            array (
                'id' => 452,
                'migration' => '2019_05_30_093419_alter_table_salary_record_add_column_title',
                'batch' => 142,
            ),
            436 => 
            array (
                'id' => 453,
                'migration' => '2019_05_30_094348_alter_table_salary_form_add_column_greetings',
                'batch' => 142,
            ),
            437 => 
            array (
                'id' => 454,
                'migration' => '2019_05_30_103454_create_api_vue_action_table',
                'batch' => 142,
            ),
            438 => 
            array (
                'id' => 455,
                'migration' => '2019_05_28_134311_create_table_api_routes',
                'batch' => 143,
            ),
            439 => 
            array (
                'id' => 456,
                'migration' => '2019_05_28_134452_create_table_api_routes_roles',
                'batch' => 143,
            ),
            440 => 
            array (
                'id' => 457,
                'migration' => '2019_05_28_134514_create_table_api_roles',
                'batch' => 143,
            ),
            441 => 
            array (
                'id' => 458,
                'migration' => '2019_05_30_104344_add_column_action_id_to_api_routes_table',
                'batch' => 143,
            ),
            442 => 
            array (
                'id' => 459,
                'migration' => '2019_05_30_111919_add_is_enable_to_meeting_room_table',
                'batch' => 143,
            ),
            443 => 
            array (
                'id' => 460,
                'migration' => '2019_05_30_113215_alter_column_route_id_as_action_id_to_api_routes_roles_table',
                'batch' => 143,
            ),
            444 => 
            array (
                'id' => 461,
                'migration' => '2019_05_30_161643_alter_message_change_column_type',
                'batch' => 143,
            ),
            445 => 
            array (
                'id' => 462,
                'migration' => '2019_05_30_165357_create_api_roles_users_table',
                'batch' => 143,
            ),
            446 => 
            array (
                'id' => 463,
                'migration' => '2019_05_30_195326_add_unique_vue_path_to_api_vue_action_table',
                'batch' => 143,
            ),
            447 => 
            array (
                'id' => 464,
                'migration' => '2019_05_31_114803_alter_pas_sale_out_warehouse_add_column_warehouse_id',
                'batch' => 144,
            ),
            448 => 
            array (
                'id' => 465,
                'migration' => '2019_05_31_150012_alter_message_add_column_title',
                'batch' => 144,
            ),
            449 => 
            array (
                'id' => 466,
                'migration' => '2019_05_31_163117_alter_pas_sale_out_warehouse_add_column_out_status',
                'batch' => 145,
            ),
            450 => 
            array (
                'id' => 467,
                'migration' => '2019_06_01_121435_alert_table_my_task_add_comment_time',
                'batch' => 145,
            ),
            451 => 
            array (
                'id' => 468,
                'migration' => '2019_06_01_122044_alert_table_my_task_alert_comment_time',
                'batch' => 145,
            ),
            452 => 
            array (
                'id' => 469,
                'migration' => '2019_06_01_175013_insert_api_routes_data',
                'batch' => 146,
            ),
            453 => 
            array (
                'id' => 470,
                'migration' => '2019_06_03_103621_alert_table_my_task_edit_add_comment_time',
                'batch' => 146,
            ),
            454 => 
            array (
                'id' => 471,
                'migration' => '2019_06_03_175340_alert_table_report_templates',
                'batch' => 147,
            ),
            455 => 
            array (
                'id' => 473,
                'migration' => '2019_06_04_115648_alter_financial_add_bank_address_table',
                'batch' => 148,
            ),
            456 => 
            array (
                'id' => 474,
                'migration' => '2019_06_04_170532_create_contract_table',
                'batch' => 149,
            ),
            457 => 
            array (
                'id' => 475,
                'migration' => '2019_06_04_190618_alert_table_my_task_add_parent_id',
                'batch' => 149,
            ),
            458 => 
            array (
                'id' => 476,
                'migration' => '2019_06_04_221819_create_vacation_extra_workflow_pass_table',
                'batch' => 150,
            ),
            459 => 
            array (
                'id' => 477,
                'migration' => '2019_06_04_221220_alert_table_my_task_add_content',
                'batch' => 151,
            ),
            460 => 
            array (
                'id' => 478,
                'migration' => '2019_06_05_004955_alert_table_my_task_add_temp_id',
                'batch' => 152,
            ),
            461 => 
            array (
                'id' => 480,
                'migration' => '2019_06_05_152325_add_code_to_pas_allot_cart_table',
                'batch' => 153,
            ),
            462 => 
            array (
                'id' => 481,
                'migration' => '2019_06_06_115415_alter_financial_customer_drop',
                'batch' => 153,
            ),
            463 => 
            array (
                'id' => 482,
                'migration' => '2019_06_06_115821_alter_financial_del_type_procs_id',
                'batch' => 153,
            ),
            464 => 
            array (
                'id' => 483,
                'migration' => '2019_06_06_115810_alter_describe_add_unique_table',
                'batch' => 154,
            ),
            465 => 
            array (
                'id' => 484,
                'migration' => '2019_06_10_093546_alter_users_family_add_has_children_table',
                'batch' => 154,
            ),
            466 => 
            array (
                'id' => 485,
                'migration' => '2019_06_10_104948_drop_to_contract_approval_table',
                'batch' => 155,
            ),
            467 => 
            array (
                'id' => 486,
                'migration' => '2019_06_10_174908_alert_table_report_templates_template_name_index',
                'batch' => 155,
            ),
            468 => 
            array (
                'id' => 487,
                'migration' => '2019_06_10_175006_alert_table_report_template_forms_drop_show_in_todo',
                'batch' => 155,
            ),
            469 => 
            array (
                'id' => 488,
                'migration' => '2019_06_11_144448_add_user_id_to_pas_allot_cart_table',
                'batch' => 155,
            ),
            470 => 
            array (
                'id' => 489,
                'migration' => '2019_06_13_115859_create_pas_stock_check_allocation_table',
                'batch' => 155,
            ),
            471 => 
            array (
                'id' => 490,
                'migration' => '2019_06_13_143418_create_table_bussiness_plan',
                'batch' => 155,
            ),
            472 => 
            array (
                'id' => 491,
                'migration' => '2019_06_13_144338_add_current_number_pas_stock_check_goods_table',
                'batch' => 155,
            ),
            473 => 
            array (
                'id' => 492,
                'migration' => '2019_06_13_145832_create_table_bussiness_category_plan',
                'batch' => 155,
            ),
            474 => 
            array (
                'id' => 493,
                'migration' => '2019_06_13_153125_drop_uinque_to_api_vue_action_table',
                'batch' => 155,
            ),
            475 => 
            array (
                'id' => 494,
                'migration' => '2019_06_13_155035_add_uinque_to_api_vue_action_table',
                'batch' => 155,
            ),
            476 => 
            array (
                'id' => 495,
                'migration' => '2019_06_13_172927_create_vacation_rule_table',
                'batch' => 155,
            ),
            477 => 
            array (
                'id' => 496,
                'migration' => '2019_06_14_133819_alert_vacation_rule_add_column_title',
                'batch' => 155,
            ),
            478 => 
            array (
                'id' => 497,
                'migration' => '2019_06_14_135619_drop_column_to_api_routes_table',
                'batch' => 155,
            ),
            479 => 
            array (
                'id' => 498,
                'migration' => '2019_06_14_150915_create_api_vue_routes_table',
                'batch' => 155,
            ),
            480 => 
            array (
                'id' => 499,
                'migration' => '2019_06_16_135148_create_financial_childer_table',
                'batch' => 156,
            ),
            481 => 
            array (
                'id' => 500,
                'migration' => '2019_06_16_161422_create_table_basic_set',
                'batch' => 156,
            ),
            482 => 
            array (
                'id' => 501,
                'migration' => '2019_06_16_200944_alter_schedules_add_report_id_column',
                'batch' => 157,
            ),
            483 => 
            array (
                'id' => 502,
                'migration' => '2019_06_16_203341_alter_reports_add_cc_ids_column',
                'batch' => 157,
            ),
            484 => 
            array (
                'id' => 503,
                'migration' => '2019_06_03_175340_alert_table_report_templates_drop_index',
                'batch' => 158,
            ),
            485 => 
            array (
                'id' => 504,
                'migration' => '2019_06_17_141820_alert_table_report_templates_drop_unique_index',
                'batch' => 158,
            ),
            486 => 
            array (
                'id' => 505,
                'migration' => '2019_06_17_155224_create_init_administrator',
                'batch' => 158,
            ),
            487 => 
            array (
                'id' => 506,
                'migration' => '2019_06_19_114046_create_abilities_table',
                'batch' => 0,
            ),
            488 => 
            array (
                'id' => 507,
                'migration' => '2019_06_19_114046_create_addwork_table',
                'batch' => 0,
            ),
            489 => 
            array (
                'id' => 508,
                'migration' => '2019_06_19_114046_create_addwork_audit_peoples_table',
                'batch' => 0,
            ),
            490 => 
            array (
                'id' => 509,
                'migration' => '2019_06_19_114046_create_addwork_company_table',
                'batch' => 0,
            ),
            491 => 
            array (
                'id' => 510,
                'migration' => '2019_06_19_114046_create_addwork_field_table',
                'batch' => 0,
            ),
            492 => 
            array (
                'id' => 511,
                'migration' => '2019_06_19_114046_create_addwork_image_table',
                'batch' => 0,
            ),
            493 => 
            array (
                'id' => 512,
                'migration' => '2019_06_19_114046_create_administrative_contract_table',
                'batch' => 0,
            ),
            494 => 
            array (
                'id' => 513,
                'migration' => '2019_06_19_114046_create_admins_table',
                'batch' => 0,
            ),
            495 => 
            array (
                'id' => 514,
                'migration' => '2019_06_19_114046_create_api_positions_roles_table',
                'batch' => 0,
            ),
            496 => 
            array (
                'id' => 515,
                'migration' => '2019_06_19_114046_create_api_roles_table',
                'batch' => 0,
            ),
            497 => 
            array (
                'id' => 516,
                'migration' => '2019_06_19_114046_create_api_roles_users_table',
                'batch' => 0,
            ),
            498 => 
            array (
                'id' => 517,
                'migration' => '2019_06_19_114046_create_api_routes_table',
                'batch' => 0,
            ),
            499 => 
            array (
                'id' => 518,
                'migration' => '2019_06_19_114046_create_api_routes_roles_table',
                'batch' => 0,
            ),
        ));
        \DB::table('migrations')->insert(array (
            0 => 
            array (
                'id' => 519,
                'migration' => '2019_06_19_114046_create_api_vue_action_table',
                'batch' => 0,
            ),
            1 => 
            array (
                'id' => 520,
                'migration' => '2019_06_19_114046_create_api_vue_routes_table',
                'batch' => 0,
            ),
            2 => 
            array (
                'id' => 521,
                'migration' => '2019_06_19_114046_create_assigned_roles_table',
                'batch' => 0,
            ),
            3 => 
            array (
                'id' => 522,
                'migration' => '2019_06_19_114046_create_attendance_annual_rule_table',
                'batch' => 0,
            ),
            4 => 
            array (
                'id' => 523,
                'migration' => '2019_06_19_114046_create_attendance_api_table',
                'batch' => 0,
            ),
            5 => 
            array (
                'id' => 524,
                'migration' => '2019_06_19_114046_create_attendance_api_anomaly_table',
                'batch' => 0,
            ),
            6 => 
            array (
                'id' => 525,
                'migration' => '2019_06_19_114046_create_attendance_api_classes_table',
                'batch' => 0,
            ),
            7 => 
            array (
                'id' => 526,
                'migration' => '2019_06_19_114046_create_attendance_api_clock_table',
                'batch' => 0,
            ),
            8 => 
            array (
                'id' => 527,
                'migration' => '2019_06_19_114046_create_attendance_api_cycle_table',
                'batch' => 0,
            ),
            9 => 
            array (
                'id' => 528,
                'migration' => '2019_06_19_114046_create_attendance_api_cycle_content_table',
                'batch' => 0,
            ),
            10 => 
            array (
                'id' => 529,
                'migration' => '2019_06_19_114046_create_attendance_api_department_table',
                'batch' => 0,
            ),
            11 => 
            array (
                'id' => 530,
                'migration' => '2019_06_19_114046_create_attendance_api_hr_update_clock_log_table',
                'batch' => 0,
            ),
            12 => 
            array (
                'id' => 531,
                'migration' => '2019_06_19_114046_create_attendance_api_national_holidays_table',
                'batch' => 0,
            ),
            13 => 
            array (
                'id' => 532,
                'migration' => '2019_06_19_114046_create_attendance_api_overtime_rule_table',
                'batch' => 0,
            ),
            14 => 
            array (
                'id' => 533,
                'migration' => '2019_06_19_114046_create_attendance_api_scheduling_table',
                'batch' => 0,
            ),
            15 => 
            array (
                'id' => 534,
                'migration' => '2019_06_19_114046_create_attendance_api_staff_table',
                'batch' => 0,
            ),
            16 => 
            array (
                'id' => 535,
                'migration' => '2019_06_19_114046_create_attendance_checkinout_table',
                'batch' => 0,
            ),
            17 => 
            array (
                'id' => 536,
                'migration' => '2019_06_19_114046_create_attendance_holidays_table',
                'batch' => 0,
            ),
            18 => 
            array (
                'id' => 537,
                'migration' => '2019_06_19_114046_create_attendance_records_table',
                'batch' => 0,
            ),
            19 => 
            array (
                'id' => 538,
                'migration' => '2019_06_19_114046_create_attendance_sheets_table',
                'batch' => 0,
            ),
            20 => 
            array (
                'id' => 539,
                'migration' => '2019_06_19_114046_create_attendance_user_info_table',
                'batch' => 0,
            ),
            21 => 
            array (
                'id' => 540,
                'migration' => '2019_06_19_114046_create_attendance_vacation_changes_table',
                'batch' => 0,
            ),
            22 => 
            array (
                'id' => 541,
                'migration' => '2019_06_19_114046_create_attendance_vacation_conversions_table',
                'batch' => 0,
            ),
            23 => 
            array (
                'id' => 542,
                'migration' => '2019_06_19_114046_create_attendance_vacation_has_croned_table',
                'batch' => 0,
            ),
            24 => 
            array (
                'id' => 543,
                'migration' => '2019_06_19_114046_create_attendance_vacations_table',
                'batch' => 0,
            ),
            25 => 
            array (
                'id' => 544,
                'migration' => '2019_06_19_114046_create_attendance_white_table',
                'batch' => 0,
            ),
            26 => 
            array (
                'id' => 545,
                'migration' => '2019_06_19_114046_create_attendance_work_classes_table',
                'batch' => 0,
            ),
            27 => 
            array (
                'id' => 546,
                'migration' => '2019_06_19_114046_create_attendance_work_user_logs_table',
                'batch' => 0,
            ),
            28 => 
            array (
                'id' => 547,
                'migration' => '2019_06_19_114046_create_attendance_workflow_leaves_table',
                'batch' => 0,
            ),
            29 => 
            array (
                'id' => 548,
                'migration' => '2019_06_19_114046_create_attendance_workflow_overtimes_table',
                'batch' => 0,
            ),
            30 => 
            array (
                'id' => 549,
                'migration' => '2019_06_19_114046_create_attendance_workflow_resumptions_table',
                'batch' => 0,
            ),
            31 => 
            array (
                'id' => 550,
                'migration' => '2019_06_19_114046_create_attendance_workflow_retroactives_table',
                'batch' => 0,
            ),
            32 => 
            array (
                'id' => 551,
                'migration' => '2019_06_19_114046_create_attendance_workflow_travels_table',
                'batch' => 0,
            ),
            33 => 
            array (
                'id' => 552,
                'migration' => '2019_06_19_114046_create_attentions_table',
                'batch' => 0,
            ),
            34 => 
            array (
                'id' => 553,
                'migration' => '2019_06_19_114046_create_basic_oa_option_table',
                'batch' => 0,
            ),
            35 => 
            array (
                'id' => 554,
                'migration' => '2019_06_19_114046_create_basic_oa_type_table',
                'batch' => 0,
            ),
            36 => 
            array (
                'id' => 555,
                'migration' => '2019_06_19_114046_create_basic_set_table',
                'batch' => 0,
            ),
            37 => 
            array (
                'id' => 556,
                'migration' => '2019_06_19_114046_create_basic_user_rank_table',
                'batch' => 0,
            ),
            38 => 
            array (
                'id' => 557,
                'migration' => '2019_06_19_114046_create_bussiness_category_plans_table',
                'batch' => 0,
            ),
            39 => 
            array (
                'id' => 558,
                'migration' => '2019_06_19_114046_create_bussiness_plans_table',
                'batch' => 0,
            ),
            40 => 
            array (
                'id' => 559,
                'migration' => '2019_06_19_114046_create_comments_table',
                'batch' => 0,
            ),
            41 => 
            array (
                'id' => 560,
                'migration' => '2019_06_19_114046_create_companies_table',
                'batch' => 0,
            ),
            42 => 
            array (
                'id' => 561,
                'migration' => '2019_06_19_114046_create_company_annual_rule_table',
                'batch' => 0,
            ),
            43 => 
            array (
                'id' => 562,
                'migration' => '2019_06_19_114046_create_company_equity_pledge_table',
                'batch' => 0,
            ),
            44 => 
            array (
                'id' => 563,
                'migration' => '2019_06_19_114046_create_company_leave_unit_table',
                'batch' => 0,
            ),
            45 => 
            array (
                'id' => 564,
                'migration' => '2019_06_19_114046_create_company_main_personnels_table',
                'batch' => 0,
            ),
            46 => 
            array (
                'id' => 565,
                'migration' => '2019_06_19_114046_create_company_seals_table',
                'batch' => 0,
            ),
            47 => 
            array (
                'id' => 566,
                'migration' => '2019_06_19_114046_create_company_seals_type_table',
                'batch' => 0,
            ),
            48 => 
            array (
                'id' => 567,
                'migration' => '2019_06_19_114046_create_company_shareholders_table',
                'batch' => 0,
            ),
            49 => 
            array (
                'id' => 568,
                'migration' => '2019_06_19_114046_create_company_vacation_table',
                'batch' => 0,
            ),
            50 => 
            array (
                'id' => 569,
                'migration' => '2019_06_19_114046_create_contract_table',
                'batch' => 0,
            ),
            51 => 
            array (
                'id' => 570,
                'migration' => '2019_06_19_114046_create_contracts_table',
                'batch' => 0,
            ),
            52 => 
            array (
                'id' => 571,
                'migration' => '2019_06_19_114046_create_corporate_assets_table',
                'batch' => 0,
            ),
            53 => 
            array (
                'id' => 572,
                'migration' => '2019_06_19_114046_create_corporate_assets_borrow_table',
                'batch' => 0,
            ),
            54 => 
            array (
                'id' => 573,
                'migration' => '2019_06_19_114046_create_corporate_assets_depreciation_table',
                'batch' => 0,
            ),
            55 => 
            array (
                'id' => 574,
                'migration' => '2019_06_19_114046_create_corporate_assets_innerdb_table',
                'batch' => 0,
            ),
            56 => 
            array (
                'id' => 575,
                'migration' => '2019_06_19_114046_create_corporate_assets_relation_table',
                'batch' => 0,
            ),
            57 => 
            array (
                'id' => 576,
                'migration' => '2019_06_19_114046_create_corporate_assets_repair_table',
                'batch' => 0,
            ),
            58 => 
            array (
                'id' => 577,
                'migration' => '2019_06_19_114046_create_corporate_assets_return_table',
                'batch' => 0,
            ),
            59 => 
            array (
                'id' => 578,
                'migration' => '2019_06_19_114046_create_corporate_assets_scrapped_table',
                'batch' => 0,
            ),
            60 => 
            array (
                'id' => 579,
                'migration' => '2019_06_19_114046_create_corporate_assets_sync_table',
                'batch' => 0,
            ),
            61 => 
            array (
                'id' => 580,
                'migration' => '2019_06_19_114046_create_corporate_assets_transfer_table',
                'batch' => 0,
            ),
            62 => 
            array (
                'id' => 581,
                'migration' => '2019_06_19_114046_create_corporate_assets_use_table',
                'batch' => 0,
            ),
            63 => 
            array (
                'id' => 582,
                'migration' => '2019_06_19_114046_create_corporate_assets_valueadded_table',
                'batch' => 0,
            ),
            64 => 
            array (
                'id' => 583,
                'migration' => '2019_06_19_114046_create_department_map_centres_table',
                'batch' => 0,
            ),
            65 => 
            array (
                'id' => 584,
                'migration' => '2019_06_19_114046_create_department_tag_table',
                'batch' => 0,
            ),
            66 => 
            array (
                'id' => 585,
                'migration' => '2019_06_19_114046_create_department_user_table',
                'batch' => 0,
            ),
            67 => 
            array (
                'id' => 586,
                'migration' => '2019_06_19_114046_create_departments_table',
                'batch' => 0,
            ),
            68 => 
            array (
                'id' => 587,
                'migration' => '2019_06_19_114046_create_describe_table',
                'batch' => 0,
            ),
            69 => 
            array (
                'id' => 588,
                'migration' => '2019_06_19_114046_create_document_table',
                'batch' => 0,
            ),
            70 => 
            array (
                'id' => 589,
                'migration' => '2019_06_19_114046_create_entry_type_table',
                'batch' => 0,
            ),
            71 => 
            array (
                'id' => 590,
                'migration' => '2019_06_19_114046_create_examined_copy_table',
                'batch' => 0,
            ),
            72 => 
            array (
                'id' => 591,
                'migration' => '2019_06_19_114046_create_executive_cars_table',
                'batch' => 0,
            ),
            73 => 
            array (
                'id' => 592,
                'migration' => '2019_06_19_114046_create_executive_cars_appoint_table',
                'batch' => 0,
            ),
            74 => 
            array (
                'id' => 593,
                'migration' => '2019_06_19_114046_create_executive_cars_record_table',
                'batch' => 0,
            ),
            75 => 
            array (
                'id' => 594,
                'migration' => '2019_06_19_114046_create_executive_cars_sendback_table',
                'batch' => 0,
            ),
            76 => 
            array (
                'id' => 595,
                'migration' => '2019_06_19_114046_create_executive_cars_use_table',
                'batch' => 0,
            ),
            77 => 
            array (
                'id' => 596,
                'migration' => '2019_06_19_114046_create_failed_jobs_table',
                'batch' => 0,
            ),
            78 => 
            array (
                'id' => 597,
                'migration' => '2019_06_19_114046_create_feedback_accssory_table',
                'batch' => 0,
            ),
            79 => 
            array (
                'id' => 598,
                'migration' => '2019_06_19_114046_create_feedback_content_table',
                'batch' => 0,
            ),
            80 => 
            array (
                'id' => 599,
                'migration' => '2019_06_19_114046_create_feedback_reply_table',
                'batch' => 0,
            ),
            81 => 
            array (
                'id' => 600,
                'migration' => '2019_06_19_114046_create_feedback_type_table',
                'batch' => 0,
            ),
            82 => 
            array (
                'id' => 601,
                'migration' => '2019_06_19_114046_create_file_storage_table',
                'batch' => 0,
            ),
            83 => 
            array (
                'id' => 602,
                'migration' => '2019_06_19_114046_create_finance_ap_table',
                'batch' => 0,
            ),
            84 => 
            array (
                'id' => 603,
                'migration' => '2019_06_19_114046_create_finance_ap_repayment_table',
                'batch' => 0,
            ),
            85 => 
            array (
                'id' => 604,
                'migration' => '2019_06_19_114046_create_finance_invoice_sign_table',
                'batch' => 0,
            ),
            86 => 
            array (
                'id' => 605,
                'migration' => '2019_06_19_114046_create_finance_ter_table',
                'batch' => 0,
            ),
            87 => 
            array (
                'id' => 606,
                'migration' => '2019_06_19_114046_create_finance_ter_item_table',
                'batch' => 0,
            ),
            88 => 
            array (
                'id' => 607,
                'migration' => '2019_06_19_114046_create_finance_workflow_payment_table',
                'batch' => 0,
            ),
            89 => 
            array (
                'id' => 608,
                'migration' => '2019_06_19_114046_create_financial_table',
                'batch' => 0,
            ),
            90 => 
            array (
                'id' => 609,
                'migration' => '2019_06_19_114046_create_financial_childer_table',
                'batch' => 0,
            ),
            91 => 
            array (
                'id' => 610,
                'migration' => '2019_06_19_114046_create_financial_detail_table',
                'batch' => 0,
            ),
            92 => 
            array (
                'id' => 611,
                'migration' => '2019_06_19_114046_create_financial_log_table',
                'batch' => 0,
            ),
            93 => 
            array (
                'id' => 612,
                'migration' => '2019_06_19_114046_create_financial_order_table',
                'batch' => 0,
            ),
            94 => 
            array (
                'id' => 613,
                'migration' => '2019_06_19_114046_create_financial_pic_table',
                'batch' => 0,
            ),
            95 => 
            array (
                'id' => 614,
                'migration' => '2019_06_19_114046_create_goods_allocation_goods_table',
                'batch' => 0,
            ),
            96 => 
            array (
                'id' => 615,
                'migration' => '2019_06_19_114046_create_growth_recode_table',
                'batch' => 0,
            ),
            97 => 
            array (
                'id' => 616,
                'migration' => '2019_06_19_114046_create_image_table',
                'batch' => 0,
            ),
            98 => 
            array (
                'id' => 617,
                'migration' => '2019_06_19_114046_create_intelligence_table',
                'batch' => 0,
            ),
            99 => 
            array (
                'id' => 618,
                'migration' => '2019_06_19_114046_create_intelligence_info_table',
                'batch' => 0,
            ),
            100 => 
            array (
                'id' => 619,
                'migration' => '2019_06_19_114046_create_intelligence_type_table',
                'batch' => 0,
            ),
            101 => 
            array (
                'id' => 620,
                'migration' => '2019_06_19_114046_create_intelligence_users_table',
                'batch' => 0,
            ),
            102 => 
            array (
                'id' => 621,
                'migration' => '2019_06_19_114046_create_investments_table',
                'batch' => 0,
            ),
            103 => 
            array (
                'id' => 622,
                'migration' => '2019_06_19_114046_create_jobs_table',
                'batch' => 0,
            ),
            104 => 
            array (
                'id' => 623,
                'migration' => '2019_06_19_114046_create_leave_table',
                'batch' => 0,
            ),
            105 => 
            array (
                'id' => 624,
                'migration' => '2019_06_19_114046_create_leave_unit_table',
                'batch' => 0,
            ),
            106 => 
            array (
                'id' => 625,
                'migration' => '2019_06_19_114046_create_leaveout_table',
                'batch' => 0,
            ),
            107 => 
            array (
                'id' => 626,
                'migration' => '2019_06_19_114046_create_leaveout_img_table',
                'batch' => 0,
            ),
            108 => 
            array (
                'id' => 627,
                'migration' => '2019_06_19_114046_create_leaveout_program_table',
                'batch' => 0,
            ),
            109 => 
            array (
                'id' => 628,
                'migration' => '2019_06_19_114046_create_likes_table',
                'batch' => 0,
            ),
            110 => 
            array (
                'id' => 629,
                'migration' => '2019_06_19_114046_create_meeting_table',
                'batch' => 0,
            ),
            111 => 
            array (
                'id' => 630,
                'migration' => '2019_06_19_114046_create_meeting_participant_table',
                'batch' => 0,
            ),
            112 => 
            array (
                'id' => 631,
                'migration' => '2019_06_19_114046_create_meeting_room_table',
                'batch' => 0,
            ),
            113 => 
            array (
                'id' => 632,
                'migration' => '2019_06_19_114046_create_meeting_room_config_table',
                'batch' => 0,
            ),
            114 => 
            array (
                'id' => 633,
                'migration' => '2019_06_19_114046_create_meeting_task_table',
                'batch' => 0,
            ),
            115 => 
            array (
                'id' => 634,
                'migration' => '2019_06_19_114046_create_message_table',
                'batch' => 0,
            ),
            116 => 
            array (
                'id' => 635,
                'migration' => '2019_06_19_114046_create_message_cron_push_records_table',
                'batch' => 0,
            ),
            117 => 
            array (
                'id' => 636,
                'migration' => '2019_06_19_114046_create_message_log_table',
                'batch' => 0,
            ),
            118 => 
            array (
                'id' => 637,
                'migration' => '2019_06_19_114046_create_message_template_table',
                'batch' => 0,
            ),
            119 => 
            array (
                'id' => 638,
                'migration' => '2019_06_19_114046_create_my_task_table',
                'batch' => 0,
            ),
            120 => 
            array (
                'id' => 639,
                'migration' => '2019_06_19_114046_create_oauth_access_tokens_table',
                'batch' => 0,
            ),
            121 => 
            array (
                'id' => 640,
                'migration' => '2019_06_19_114046_create_oauth_auth_codes_table',
                'batch' => 0,
            ),
            122 => 
            array (
                'id' => 641,
                'migration' => '2019_06_19_114046_create_oauth_clients_table',
                'batch' => 0,
            ),
            123 => 
            array (
                'id' => 642,
                'migration' => '2019_06_19_114046_create_oauth_personal_access_clients_table',
                'batch' => 0,
            ),
            124 => 
            array (
                'id' => 643,
                'migration' => '2019_06_19_114046_create_oauth_refresh_tokens_table',
                'batch' => 0,
            ),
            125 => 
            array (
                'id' => 644,
                'migration' => '2019_06_19_114046_create_operate_log_table',
                'batch' => 0,
            ),
            126 => 
            array (
                'id' => 645,
                'migration' => '2019_06_19_114046_create_pas_allot_card_goods_table',
                'batch' => 0,
            ),
            127 => 
            array (
                'id' => 646,
                'migration' => '2019_06_19_114046_create_pas_allot_cart_table',
                'batch' => 0,
            ),
            128 => 
            array (
                'id' => 647,
                'migration' => '2019_06_19_114046_create_pas_attributes_table',
                'batch' => 0,
            ),
            129 => 
            array (
                'id' => 648,
                'migration' => '2019_06_19_114046_create_pas_brands_table',
                'batch' => 0,
            ),
            130 => 
            array (
                'id' => 649,
                'migration' => '2019_06_19_114046_create_pas_categorys_table',
                'batch' => 0,
            ),
            131 => 
            array (
                'id' => 650,
                'migration' => '2019_06_19_114046_create_pas_commodity_category_table',
                'batch' => 0,
            ),
            132 => 
            array (
                'id' => 651,
                'migration' => '2019_06_19_114046_create_pas_cost_information_table',
                'batch' => 0,
            ),
            133 => 
            array (
                'id' => 652,
                'migration' => '2019_06_19_114046_create_pas_goods_table',
                'batch' => 0,
            ),
            134 => 
            array (
                'id' => 653,
                'migration' => '2019_06_19_114046_create_pas_goods_allocation_table',
                'batch' => 0,
            ),
            135 => 
            array (
                'id' => 654,
                'migration' => '2019_06_19_114046_create_pas_goods_attributes_table',
                'batch' => 0,
            ),
            136 => 
            array (
                'id' => 655,
                'migration' => '2019_06_19_114046_create_pas_goods_flow_table',
                'batch' => 0,
            ),
            137 => 
            array (
                'id' => 656,
                'migration' => '2019_06_19_114046_create_pas_goods_specific_prices_table',
                'batch' => 0,
            ),
            138 => 
            array (
                'id' => 657,
                'migration' => '2019_06_19_114046_create_pas_goods_specifics_table',
                'batch' => 0,
            ),
            139 => 
            array (
                'id' => 658,
                'migration' => '2019_06_19_114046_create_pas_logistics_table',
                'batch' => 0,
            ),
            140 => 
            array (
                'id' => 659,
                'migration' => '2019_06_19_114046_create_pas_logistics_point_table',
                'batch' => 0,
            ),
            141 => 
            array (
                'id' => 660,
                'migration' => '2019_06_19_114046_create_pas_payment_order_table',
                'batch' => 0,
            ),
            142 => 
            array (
                'id' => 661,
                'migration' => '2019_06_19_114046_create_pas_payment_order_content_table',
                'batch' => 0,
            ),
            143 => 
            array (
                'id' => 662,
                'migration' => '2019_06_19_114046_create_pas_purchase_table',
                'batch' => 0,
            ),
            144 => 
            array (
                'id' => 663,
                'migration' => '2019_06_19_114046_create_pas_purchase_commodity_table',
                'batch' => 0,
            ),
            145 => 
            array (
                'id' => 664,
                'migration' => '2019_06_19_114046_create_pas_purchase_commodity_content_table',
                'batch' => 0,
            ),
            146 => 
            array (
                'id' => 665,
                'migration' => '2019_06_19_114046_create_pas_purchase_payable_money_table',
                'batch' => 0,
            ),
            147 => 
            array (
                'id' => 666,
                'migration' => '2019_06_19_114046_create_pas_return_order_table',
                'batch' => 0,
            ),
            148 => 
            array (
                'id' => 667,
                'migration' => '2019_06_19_114046_create_pas_sale_invoices_table',
                'batch' => 0,
            ),
            149 => 
            array (
                'id' => 668,
                'migration' => '2019_06_19_114046_create_pas_sale_order_goods_table',
                'batch' => 0,
            ),
            150 => 
            array (
                'id' => 669,
                'migration' => '2019_06_19_114046_create_pas_sale_orders_table',
                'batch' => 0,
            ),
            151 => 
            array (
                'id' => 670,
                'migration' => '2019_06_19_114046_create_pas_sale_out_warehouse_table',
                'batch' => 0,
            ),
            152 => 
            array (
                'id' => 671,
                'migration' => '2019_06_19_114046_create_pas_sale_out_warehouse_goods_table',
                'batch' => 0,
            ),
            153 => 
            array (
                'id' => 672,
                'migration' => '2019_06_19_114046_create_pas_sale_return_in_warehouse_table',
                'batch' => 0,
            ),
            154 => 
            array (
                'id' => 673,
                'migration' => '2019_06_19_114046_create_pas_sale_return_in_warehouse_goods_table',
                'batch' => 0,
            ),
            155 => 
            array (
                'id' => 674,
                'migration' => '2019_06_19_114046_create_pas_sale_return_order_goods_table',
                'batch' => 0,
            ),
            156 => 
            array (
                'id' => 675,
                'migration' => '2019_06_19_114046_create_pas_sale_return_orders_table',
                'batch' => 0,
            ),
            157 => 
            array (
                'id' => 676,
                'migration' => '2019_06_19_114046_create_pas_specific_items_table',
                'batch' => 0,
            ),
            158 => 
            array (
                'id' => 677,
                'migration' => '2019_06_19_114046_create_pas_specifics_table',
                'batch' => 0,
            ),
            159 => 
            array (
                'id' => 678,
                'migration' => '2019_06_19_114046_create_pas_stock_check_table',
                'batch' => 0,
            ),
            160 => 
            array (
                'id' => 679,
                'migration' => '2019_06_19_114046_create_pas_stock_check_allocation_table',
                'batch' => 0,
            ),
            161 => 
            array (
                'id' => 680,
                'migration' => '2019_06_19_114046_create_pas_stock_check_goods_table',
                'batch' => 0,
            ),
            162 => 
            array (
                'id' => 681,
                'migration' => '2019_06_19_114046_create_pas_supplier_table',
                'batch' => 0,
            ),
            163 => 
            array (
                'id' => 682,
                'migration' => '2019_06_19_114046_create_pas_warehouse_table',
                'batch' => 0,
            ),
            164 => 
            array (
                'id' => 683,
                'migration' => '2019_06_19_114046_create_pas_warehouse_delivery_type_table',
                'batch' => 0,
            ),
            165 => 
            array (
                'id' => 684,
                'migration' => '2019_06_19_114046_create_pas_warehouse_goods_location_table',
                'batch' => 0,
            ),
            166 => 
            array (
                'id' => 685,
                'migration' => '2019_06_19_114046_create_pas_warehouse_in_card_table',
                'batch' => 0,
            ),
            167 => 
            array (
                'id' => 686,
                'migration' => '2019_06_19_114046_create_pas_warehouse_in_goods_table',
                'batch' => 0,
            ),
            168 => 
            array (
                'id' => 687,
                'migration' => '2019_06_19_114046_create_pas_warehouse_out_card_table',
                'batch' => 0,
            ),
            169 => 
            array (
                'id' => 688,
                'migration' => '2019_06_19_114046_create_pas_warehouse_out_goods_table',
                'batch' => 0,
            ),
            170 => 
            array (
                'id' => 689,
                'migration' => '2019_06_19_114046_create_pas_warehousing_apply_table',
                'batch' => 0,
            ),
            171 => 
            array (
                'id' => 690,
                'migration' => '2019_06_19_114046_create_pas_warehousing_apply_content_table',
                'batch' => 0,
            ),
            172 => 
            array (
                'id' => 691,
                'migration' => '2019_06_19_114046_create_password_resets_table',
                'batch' => 0,
            ),
            173 => 
            array (
                'id' => 692,
                'migration' => '2019_06_19_114046_create_pending_users_table',
                'batch' => 0,
            ),
            174 => 
            array (
                'id' => 693,
                'migration' => '2019_06_19_114046_create_performance_application_table',
                'batch' => 0,
            ),
            175 => 
            array (
                'id' => 694,
                'migration' => '2019_06_19_114046_create_performance_application_content_table',
                'batch' => 0,
            ),
            176 => 
            array (
                'id' => 695,
                'migration' => '2019_06_19_114046_create_performance_application_son_table',
                'batch' => 0,
            ),
            177 => 
            array (
                'id' => 696,
                'migration' => '2019_06_19_114046_create_performance_template_table',
                'batch' => 0,
            ),
            178 => 
            array (
                'id' => 697,
                'migration' => '2019_06_19_114046_create_performance_template_content_table',
                'batch' => 0,
            ),
            179 => 
            array (
                'id' => 698,
                'migration' => '2019_06_19_114046_create_performance_template_quota_table',
                'batch' => 0,
            ),
            180 => 
            array (
                'id' => 699,
                'migration' => '2019_06_19_114046_create_performance_template_son_table',
                'batch' => 0,
            ),
            181 => 
            array (
                'id' => 700,
                'migration' => '2019_06_19_114046_create_permissions_table',
                'batch' => 0,
            ),
            182 => 
            array (
                'id' => 701,
                'migration' => '2019_06_19_114046_create_position_table',
                'batch' => 0,
            ),
            183 => 
            array (
                'id' => 702,
                'migration' => '2019_06_19_114046_create_position_department_table',
                'batch' => 0,
            ),
            184 => 
            array (
                'id' => 703,
                'migration' => '2019_06_19_114046_create_profiles_table',
                'batch' => 0,
            ),
            185 => 
            array (
                'id' => 704,
                'migration' => '2019_06_19_114046_create_profit_projects_table',
                'batch' => 0,
            ),
            186 => 
            array (
                'id' => 705,
                'migration' => '2019_06_19_114046_create_punishment_template_table',
                'batch' => 0,
            ),
            187 => 
            array (
                'id' => 706,
                'migration' => '2019_06_19_114046_create_punishment_template_content_table',
                'batch' => 0,
            ),
            188 => 
            array (
                'id' => 707,
                'migration' => '2019_06_19_114046_create_report_complain_table',
                'batch' => 0,
            ),
            189 => 
            array (
                'id' => 708,
                'migration' => '2019_06_19_114046_create_report_rules_table',
                'batch' => 0,
            ),
            190 => 
            array (
                'id' => 709,
                'migration' => '2019_06_19_114046_create_report_template_forms_table',
                'batch' => 0,
            ),
            191 => 
            array (
                'id' => 710,
                'migration' => '2019_06_19_114046_create_report_templates_table',
                'batch' => 0,
            ),
            192 => 
            array (
                'id' => 711,
                'migration' => '2019_06_19_114046_create_reports_table',
                'batch' => 0,
            ),
            193 => 
            array (
                'id' => 712,
                'migration' => '2019_06_19_114046_create_roles_table',
                'batch' => 0,
            ),
            194 => 
            array (
                'id' => 713,
                'migration' => '2019_06_19_114046_create_salary_attendance_table',
                'batch' => 0,
            ),
            195 => 
            array (
                'id' => 714,
                'migration' => '2019_06_19_114046_create_salary_form_table',
                'batch' => 0,
            ),
            196 => 
            array (
                'id' => 715,
                'migration' => '2019_06_19_114046_create_salary_record_table',
                'batch' => 0,
            ),
            197 => 
            array (
                'id' => 716,
                'migration' => '2019_06_19_114046_create_salary_record_sync_type_table',
                'batch' => 0,
            ),
            198 => 
            array (
                'id' => 717,
                'migration' => '2019_06_19_114046_create_salary_reward_punishment_table',
                'batch' => 0,
            ),
            199 => 
            array (
                'id' => 718,
                'migration' => '2019_06_19_114046_create_salary_reward_punishment_complain_table',
                'batch' => 0,
            ),
            200 => 
            array (
                'id' => 719,
                'migration' => '2019_06_19_114046_create_salary_social_security_table',
                'batch' => 0,
            ),
            201 => 
            array (
                'id' => 720,
                'migration' => '2019_06_19_114046_create_schedules_table',
                'batch' => 0,
            ),
            202 => 
            array (
                'id' => 721,
                'migration' => '2019_06_19_114046_create_seal_change_logs_table',
                'batch' => 0,
            ),
            203 => 
            array (
                'id' => 722,
                'migration' => '2019_06_19_114046_create_seals_table',
                'batch' => 0,
            ),
            204 => 
            array (
                'id' => 723,
                'migration' => '2019_06_19_114046_create_sessions_table',
                'batch' => 0,
            ),
            205 => 
            array (
                'id' => 724,
                'migration' => '2019_06_19_114046_create_social_security_table',
                'batch' => 0,
            ),
            206 => 
            array (
                'id' => 725,
                'migration' => '2019_06_19_114046_create_social_security_relation_table',
                'batch' => 0,
            ),
            207 => 
            array (
                'id' => 726,
                'migration' => '2019_06_19_114046_create_supervises_table',
                'batch' => 0,
            ),
            208 => 
            array (
                'id' => 727,
                'migration' => '2019_06_19_114046_create_tag_user_table',
                'batch' => 0,
            ),
            209 => 
            array (
                'id' => 728,
                'migration' => '2019_06_19_114046_create_tags_table',
                'batch' => 0,
            ),
            210 => 
            array (
                'id' => 729,
                'migration' => '2019_06_19_114046_create_task_table',
                'batch' => 0,
            ),
            211 => 
            array (
                'id' => 730,
                'migration' => '2019_06_19_114046_create_task_score_table',
                'batch' => 0,
            ),
            212 => 
            array (
                'id' => 731,
                'migration' => '2019_06_19_114046_create_task_score_log_by_month_table',
                'batch' => 0,
            ),
            213 => 
            array (
                'id' => 732,
                'migration' => '2019_06_19_114046_create_total_audit_table',
                'batch' => 0,
            ),
            214 => 
            array (
                'id' => 733,
                'migration' => '2019_06_19_114046_create_total_comment_table',
                'batch' => 0,
            ),
            215 => 
            array (
                'id' => 734,
                'migration' => '2019_06_19_114046_create_transaction_logs_table',
                'batch' => 0,
            ),
            216 => 
            array (
                'id' => 735,
                'migration' => '2019_06_19_114046_create_trip_table',
                'batch' => 0,
            ),
            217 => 
            array (
                'id' => 736,
                'migration' => '2019_06_19_114046_create_trip_agenda_table',
                'batch' => 0,
            ),
            218 => 
            array (
                'id' => 737,
                'migration' => '2019_06_19_114046_create_trip_user_table',
                'batch' => 0,
            ),
            219 => 
            array (
                'id' => 738,
                'migration' => '2019_06_19_114046_create_user_account_records_table',
                'batch' => 0,
            ),
            220 => 
            array (
                'id' => 739,
                'migration' => '2019_06_19_114046_create_user_accounts_table',
                'batch' => 0,
            ),
            221 => 
            array (
                'id' => 740,
                'migration' => '2019_06_19_114046_create_user_bank_card_table',
                'batch' => 0,
            ),
            222 => 
            array (
                'id' => 741,
                'migration' => '2019_06_19_114046_create_user_family_table',
                'batch' => 0,
            ),
            223 => 
            array (
                'id' => 742,
                'migration' => '2019_06_19_114046_create_user_log_table',
                'batch' => 0,
            ),
            224 => 
            array (
                'id' => 743,
                'migration' => '2019_06_19_114046_create_user_schedules_table',
                'batch' => 0,
            ),
            225 => 
            array (
                'id' => 744,
                'migration' => '2019_06_19_114046_create_user_urgent_contacts_table',
                'batch' => 0,
            ),
            226 => 
            array (
                'id' => 745,
                'migration' => '2019_06_19_114046_create_user_vacation_table',
                'batch' => 0,
            ),
            227 => 
            array (
                'id' => 746,
                'migration' => '2019_06_19_114046_create_users_table',
                'batch' => 0,
            ),
            228 => 
            array (
                'id' => 747,
                'migration' => '2019_06_19_114046_create_users_detail_info_table',
                'batch' => 0,
            ),
            229 => 
            array (
                'id' => 748,
                'migration' => '2019_06_19_114046_create_users_dimission_table',
                'batch' => 0,
            ),
            230 => 
            array (
                'id' => 749,
                'migration' => '2019_06_19_114046_create_users_salary_table',
                'batch' => 0,
            ),
            231 => 
            array (
                'id' => 750,
                'migration' => '2019_06_19_114046_create_users_salary_data_table',
                'batch' => 0,
            ),
            232 => 
            array (
                'id' => 751,
                'migration' => '2019_06_19_114046_create_users_salary_relation_table',
                'batch' => 0,
            ),
            233 => 
            array (
                'id' => 752,
                'migration' => '2019_06_19_114046_create_users_salary_template_table',
                'batch' => 0,
            ),
            234 => 
            array (
                'id' => 753,
                'migration' => '2019_06_19_114046_create_users_turnover_stats_table',
                'batch' => 0,
            ),
            235 => 
            array (
                'id' => 754,
                'migration' => '2019_06_19_114046_create_vacation_business_trip_record_table',
                'batch' => 0,
            ),
            236 => 
            array (
                'id' => 755,
                'migration' => '2019_06_19_114046_create_vacation_extra_table',
                'batch' => 0,
            ),
            237 => 
            array (
                'id' => 756,
                'migration' => '2019_06_19_114046_create_vacation_extra_workflow_pass_table',
                'batch' => 0,
            ),
            238 => 
            array (
                'id' => 757,
                'migration' => '2019_06_19_114046_create_vacation_leave_record_table',
                'batch' => 0,
            ),
            239 => 
            array (
                'id' => 758,
                'migration' => '2019_06_19_114046_create_vacation_outside_record_table',
                'batch' => 0,
            ),
            240 => 
            array (
                'id' => 759,
                'migration' => '2019_06_19_114046_create_vacation_patch_record_table',
                'batch' => 0,
            ),
            241 => 
            array (
                'id' => 760,
                'migration' => '2019_06_19_114046_create_vacation_rule_table',
                'batch' => 0,
            ),
            242 => 
            array (
                'id' => 761,
                'migration' => '2019_06_19_114046_create_vacation_trip_record_table',
                'batch' => 0,
            ),
            243 => 
            array (
                'id' => 762,
                'migration' => '2019_06_19_114046_create_vacation_type_table',
                'batch' => 0,
            ),
            244 => 
            array (
                'id' => 763,
                'migration' => '2019_06_19_114046_create_vacations_table',
                'batch' => 0,
            ),
            245 => 
            array (
                'id' => 764,
                'migration' => '2019_06_19_114046_create_vote_table',
                'batch' => 0,
            ),
            246 => 
            array (
                'id' => 765,
                'migration' => '2019_06_19_114046_create_vote_department_table',
                'batch' => 0,
            ),
            247 => 
            array (
                'id' => 766,
                'migration' => '2019_06_19_114046_create_vote_option_table',
                'batch' => 0,
            ),
            248 => 
            array (
                'id' => 767,
                'migration' => '2019_06_19_114046_create_vote_participant_table',
                'batch' => 0,
            ),
            249 => 
            array (
                'id' => 768,
                'migration' => '2019_06_19_114046_create_vote_record_table',
                'batch' => 0,
            ),
            250 => 
            array (
                'id' => 769,
                'migration' => '2019_06_19_114046_create_vote_rule_table',
                'batch' => 0,
            ),
            251 => 
            array (
                'id' => 770,
                'migration' => '2019_06_19_114046_create_vote_type_table',
                'batch' => 0,
            ),
            252 => 
            array (
                'id' => 771,
                'migration' => '2019_06_19_114046_create_welfare_table',
                'batch' => 0,
            ),
            253 => 
            array (
                'id' => 772,
                'migration' => '2019_06_19_114046_create_welfare_receiver_table',
                'batch' => 0,
            ),
            254 => 
            array (
                'id' => 773,
                'migration' => '2019_06_19_114046_create_workflow_authorize_agent_table',
                'batch' => 0,
            ),
            255 => 
            array (
                'id' => 774,
                'migration' => '2019_06_19_114046_create_workflow_entries_table',
                'batch' => 0,
            ),
            256 => 
            array (
                'id' => 775,
                'migration' => '2019_06_19_114046_create_workflow_entry_data_table',
                'batch' => 0,
            ),
            257 => 
            array (
                'id' => 776,
                'migration' => '2019_06_19_114046_create_workflow_flow_links_table',
                'batch' => 0,
            ),
            258 => 
            array (
                'id' => 777,
                'migration' => '2019_06_19_114046_create_workflow_flow_types_table',
                'batch' => 0,
            ),
            259 => 
            array (
                'id' => 778,
                'migration' => '2019_06_19_114046_create_workflow_flows_table',
                'batch' => 0,
            ),
            260 => 
            array (
                'id' => 779,
                'migration' => '2019_06_19_114046_create_workflow_messages_table',
                'batch' => 0,
            ),
            261 => 
            array (
                'id' => 780,
                'migration' => '2019_06_19_114046_create_workflow_process_var_table',
                'batch' => 0,
            ),
            262 => 
            array (
                'id' => 781,
                'migration' => '2019_06_19_114046_create_workflow_processes_table',
                'batch' => 0,
            ),
            263 => 
            array (
                'id' => 782,
                'migration' => '2019_06_19_114046_create_workflow_procs_table',
                'batch' => 0,
            ),
            264 => 
            array (
                'id' => 783,
                'migration' => '2019_06_19_114046_create_workflow_role_table',
                'batch' => 0,
            ),
            265 => 
            array (
                'id' => 784,
                'migration' => '2019_06_19_114046_create_workflow_role_user_table',
                'batch' => 0,
            ),
            266 => 
            array (
                'id' => 785,
                'migration' => '2019_06_19_114046_create_workflow_task_table',
                'batch' => 0,
            ),
            267 => 
            array (
                'id' => 786,
                'migration' => '2019_06_19_114046_create_workflow_template_forms_table',
                'batch' => 0,
            ),
            268 => 
            array (
                'id' => 787,
                'migration' => '2019_06_19_114046_create_workflow_templates_table',
                'batch' => 0,
            ),
            269 => 
            array (
                'id' => 788,
                'migration' => '2019_06_19_114046_create_workflow_user_sync_table',
                'batch' => 0,
            ),
            270 => 
            array (
                'id' => 789,
                'migration' => '2019_06_19_114118_add_foreign_keys_to_api_vue_routes_table',
                'batch' => 0,
            ),
            271 => 
            array (
                'id' => 790,
                'migration' => '2019_06_19_114118_add_foreign_keys_to_assigned_roles_table',
                'batch' => 0,
            ),
            272 => 
            array (
                'id' => 791,
                'migration' => '2019_06_19_114118_add_foreign_keys_to_permissions_table',
                'batch' => 0,
            ),
            273 => 
            array (
                'id' => 792,
                'migration' => '2019_06_19_114118_add_foreign_keys_to_user_schedules_table',
                'batch' => 0,
            ),
            274 => 
            array (
                'id' => 793,
                'migration' => '2019_06_19_114118_add_foreign_keys_to_users_salary_table',
                'batch' => 0,
            ),
            275 => 
            array (
                'id' => 794,
                'migration' => '2019_06_19_114118_add_foreign_keys_to_users_salary_data_table',
                'batch' => 0,
            ),
            276 => 
            array (
                'id' => 795,
                'migration' => '2019_06_19_114118_add_foreign_keys_to_users_salary_relation_table',
                'batch' => 0,
            ),
        ));
        
        
    }
}