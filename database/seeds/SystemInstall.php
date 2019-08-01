<?php

use Illuminate\Database\Seeder;

class SystemInstall extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SystemInstallAbilitiesTableSeeder::class);
        $this->call(SystemInstallAddworkCompanyTableSeeder::class);
        $this->call(SystemInstallAssignedRolesTableSeeder::class);
        $this->call(SystemInstallAttendanceAnnualRuleTableSeeder::class);
        $this->call(SystemInstallCompaniesTableSeeder::class);
        $this->call(SystemInstallDepartmentsTableSeeder::class);
        $this->call(SystemInstallMigrationsTableSeeder::class);
        $this->call(SystemInstallPermissionsTableSeeder::class);
        $this->call(SystemInstallRolesTableSeeder::class);
        $this->call(SystemInstallUsersTableSeeder::class);
        $this->call(SystemInstallVacationTypeTableSeeder::class);
        $this->call(SystemInstallVoteRuleTableSeeder::class);
        $this->call(SystemInstallVoteTypeTableSeeder::class);
        $this->call(SystemInstallWorkflowFlowLinksTableSeeder::class);
        $this->call(SystemInstallWorkflowFlowTypesTableSeeder::class);
        $this->call(SystemInstallWorkflowFlowsTableSeeder::class);
        $this->call(SystemInstallWorkflowMessagesTableSeeder::class);
        $this->call(SystemInstallWorkflowProcessVarTableSeeder::class);
        $this->call(SystemInstallWorkflowProcessesTableSeeder::class);
        $this->call(SystemInstallWorkflowRoleTableSeeder::class);
        $this->call(SystemInstallWorkflowTemplateFormsTableSeeder::class);
        $this->call(SystemInstallWorkflowTemplatesTableSeeder::class);
        $this->call(SystemInstallAttendanceApiTableSeeder::class);
        $this->call(SystemInstallAttendanceApiClassesTableSeeder::class);
        $this->call(SystemInstallAttendanceApiNationalHolidaysTableSeeder::class);
        $this->call(SystemInstallAttendanceApiOvertimeRuleTableSeeder::class);
    }
}
