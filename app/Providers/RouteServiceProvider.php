<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web/admin.php');
            require base_path('routes/web/attendance.php');
            require base_path('routes/web/workflow.php');
            require base_path('routes/web.php');
            require base_path('routes/users/users.php');
            require base_path('routes/feedback/feedback.php');
            require base_path('routes/addwork/addwork.php');
            require base_path('routes/vacations/vacations.php');
            require base_path('routes/performance/performance.php');
            require base_path('routes/comments/comments.php');
            require base_path('routes/Alioss.php');
            require base_path('routes/complain/complain.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *welfare
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'prefix'=>'api',
            'middleware' => 'api',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api.php');
            require base_path('routes/Alioss.php');
            require base_path('routes/users/pending_users.php');
            require base_path('routes/workflow/entry.php');
            require base_path('routes/attendance/attendance.php');
            require base_path('routes/attendanceApi/attendanceApi.php');
            require base_path('routes/attendanceApi/attendanceApiCount.php');
            require base_path('routes/leaveout/leaveout.php');
            require base_path('routes/roster/roster.php');
            require base_path('routes/vote/vote.php');
            require base_path('routes/salary/salary.php');
            require base_path('routes/task/task.php');
            require base_path('routes/socialsecurity/socialsecurity.php');
            require base_path('routes/message/message.php');
			require base_path('routes/workflow/entry.php');
			require base_path('routes/workflow/finance.php');
            require base_path('routes/workflow/positive.php');
            require base_path('routes/workflow/contract.php');
            require base_path('routes/meeting/meeting.php');
            require base_path('routes/report/report.php');
            require base_path('routes/likes/likes.php');
            require base_path('routes/supervise/supervise.php');
            require base_path('routes/attention/attention.php');
            require base_path('routes/schedules/schedules.php');
            require base_path('routes/users/leave.php');
            require base_path('routes/seals/seals.php');
            require base_path('routes/document/document.php');
            require base_path('routes/intelligence/intelligence.php');
            require base_path('routes/inspector/inspector.php');
            require base_path('routes/administrative/contract.php');
            require base_path('routes/executive/cars.php');
            require base_path('routes/pas/goods/create.php');
            require base_path('routes/pas/purchase/purchase.php');
            require base_path('routes/flowcustomize/flowcustomize.php');
            require base_path('routes/pas/warehouse/warehouse.php');
            require base_path('routes/salary/form.php');
			require base_path('routes/workflow/welfare.php');
            require base_path('routes/corporateassets/corporateassets.php');
            require base_path('routes/salary/reward_punishment.php');
            require base_path('routes/salary/score.php');
            require base_path('routes/pas/goods/sale.php');
            require base_path('routes/power/power.php');
            require base_path('routes/financial/bussiness_plan.php');
        });
    }
}
