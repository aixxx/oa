<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Attempting' => [
            'App\Listeners\LogAuthenticationAttempt',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\LogFailedLogin',
        ],

        'Illuminate\Auth\Events\PasswordReset' => [
            'App\Listeners\LogPasswordReset',
        ],
        'App\Events\OutsidePunchEvent' => [
            'App\Listeners\OutsidePunchListener',
        ],
        'App\Events\ExtraAuditEvent' => [
            'App\Listeners\ExtraAuditListener',
        ],
        'App\Events\AttendanceLeaveEvent' => [
            'App\Listeners\AttendanceLeaveListener',
        ],
        'App\Events\BusinessTripEvent' => [
            'App\Listeners\BusinessTripListener',
        ],
        'App\Events\PositiveEvent' => [
            'App\Listeners\PositiveListener',
        ],
        'App\Events\UserPositiveEvent' => [
            'App\Listeners\PositiveStartListener',
        ],
        'App\Events\ContractPassEvent' => [
            'App\Listeners\ContractPassListener',
        ],
        'App\Events\ContractRejectEvent' => [
            'App\Listeners\ContractRejectListener',
        ],
        'App\Events\VacationPatchEvent' => [
            'App\Listeners\VacationPatchListener',
        ],
        'App\Events\WageEvent' => [
            'App\Listeners\WageListener',
        ],
        'App\Events\FlowBusinessTripLogEvent' => [
            'App\Listeners\FlowBusinessTripLogListener',
        ],
        'App\Events\FlowExtraLogEvent' => [
            'App\Listeners\FlowExtraLogListener',
        ],
        'App\Events\FlowLeaveLogEvent' => [
            'App\Listeners\FlowLeaveLogListener',
        ],
        'App\Events\FlowOutSideLogEvent' => [
            'App\Listeners\FlowOutsideLogListener',
        ],
        'App\Events\FlowPatchLogEvent' => [
            'App\Listeners\FlowPatchLogListener',
        ],
        'App\Events\WageEndEvent' => [
            'App\Listeners\WageEndListener',
        ],
        'App\Events\WageStartEvent' => [
            'App\Listeners\WageStartListener',
        ],
        'App\Events\AdministrativeContractCentreEvent' => [
            'App\Listeners\AdministrativeContractCentreListener',
        ],
        'App\Events\MessingRejectEvent' => [
            'App\Listeners\MessingRejectListener',
        ],
        'App\Events\MessingPassEvent' => [
            'App\Listeners\MessingPassListener',
        ],
        'App\Events\GoToWantedContractEvent' => [
            'App\Listeners\GoToWantedContractListener',
        ],
        'App\Events\GoToWantedHandOverEvent' => [
            'App\Listeners\GoToWantedHandOverListener',
        ],
        'App\Events\GoToStatusActiveLeaveEvent' => [
            'App\Listeners\GoToStatusActiveLeaveListener',
        ],
        'App\Events\GoToStatusConfirmLeaveEvent' => [
            'App\Listeners\GoToStatusConfirmLeaveListener',
        ],
        'App\Events\CreateUserAccountEvent' => [
            'App\Listeners\CreateUserAccountEventListener',
        ],
		'App\Events\PerformanceEvent' => [
            'App\Listeners\PerformanceListener',
        ],
		
		'App\Events\FeeExpenseEvent' => [
            'App\Listeners\FeeExpenseListener',
        ],
		
		'App\Events\FeeExpenseEndEvent' => [
            'App\Listeners\FeeExpenseEndListener',
        ],
        
        'App\Events\FlowCustomizePass' => [
            'App\Listeners\FlowCustomizePassListener',
        ],
        'App\Events\FlowCustomizeReject' => [
            'App\Listeners\FlowCustomizeRejectListener',
        ],
        'App\Events\InteStartEvent' => [
            'App\Listeners\InteStartListener',
        ],
        'App\Events\InteEndEvent' => [
            'App\Listeners\InteEndListener',
        ],
        'App\Events\InteRefusedEvent' => [
            'App\Listeners\InteRefusedListener',
        ],
        'App\Events\InspectorStartEvent' => [
            'App\Listeners\InspectorStartListener',
        ],
        'App\Events\InspectorEndEvent' => [
            'App\Listeners\InspectorEndListener',
        ],
        'App\Events\PurchasePassEvent' => [
            'App\Listeners\PurchasePassListener',
        ],
        'App\Events\PurchaseRejectEvent' => [
            'App\Listeners\PurchaseRejectListener',
        ],
        'App\Events\ReturnOrderPassEvent' => [
            'App\Listeners\ReturnOrderPassListener',
        ],
        'App\Events\ReturnOrderRejectEvent' => [
            'App\Listeners\ReturnOrderRejectListener',
        ],

        'App\Events\PaymentOrderPassEvent' => [
            'App\Listeners\PaymentOrderPassListener',
        ],
        'App\Events\PaymentOrderRejectEvent' => [
            'App\Listeners\PaymentOrderRejectListener',
        ],
        'App\Events\SaleOrderPassEvent' => [
            'App\Listeners\SaleOrderPassListener',
        ],
        'App\Events\SaleOrderRejectEvent' => [
            'App\Listeners\SaleOrderRejectListener',
        ],
        'App\Events\CorporateAssetsBorrowPassEvent' => [
            'App\Listeners\CorporateAssetsBorrowPassListener',
        ],
        'App\Events\CorporateAssetsBorrowRejectEvent' => [
            'App\Listeners\CorporateAssetsBorrowRejectListener',
        ],
        'App\Events\CorporateAssetsUsePassEvent' => [
            'App\Listeners\CorporateAssetsUsePassListener',
        ],
        'App\Events\CorporateAssetsUseRejectEvent' => [
            'App\Listeners\CorporateAssetsUseRejectListener',
        ],
        'App\Events\CorporateAssetsReturnPassEvent' => [
            'App\Listeners\CorporateAssetsReturnPassListener',
        ],
        'App\Events\CorporateAssetsReturnRejectEvent' => [
            'App\Listeners\CorporateAssetsReturnRejectListener',
        ],
        'App\Events\CorporateAssetsRepairPassEvent' => [
            'App\Listeners\CorporateAssetsRepairPassListener',
        ],
        'App\Events\CorporateAssetsRepairRejectEvent' => [
            'App\Listeners\CorporateAssetsRepairRejectListener',
        ],
        'App\Events\CorporateAssetsTransferPassEvent' => [
            'App\Listeners\CorporateAssetsTransferPassListener',
        ],
        'App\Events\CorporateAssetsTransferRejectEvent' => [
            'App\Listeners\CorporateAssetsTransferRejectListener',
        ],
        'App\Events\CorporateAssetsScrappedPassEvent' => [
            'App\Listeners\CorporateAssetsScrappedPassListener',
        ],
        'App\Events\CorporateAssetsScrappedRejectEvent' => [
            'App\Listeners\CorporateAssetsScrappedRejectListener',
        ],
        'App\Events\CorporateAssetsValueaddedPassEvent' => [
            'App\Listeners\CorporateAssetsValueaddedPassListener',
        ],
        'App\Events\CorporateAssetsValueaddedRejectEvent' => [
            'App\Listeners\CorporateAssetsValueaddedRejectListener',
        ],
        'App\Events\CorporateAssetsDepreciationPassEvent' => [
            'App\Listeners\CorporateAssetsDepreciationPassListener',
        ],
        'App\Events\CorporateAssetsDepreciationRejectEvent' => [
            'App\Listeners\CorporateAssetsDepreciationRejectListener',
        ],
        'App\Events\PassSalaryRecordEvent' => [
            'App\Listeners\PassSalaryRecordListener',
        ],
        'App\Events\WorkflowPassEvent' => [
            'App\Listeners\WorkflowPassListener',
        ],
        'App\Events\WorkflowRejecEvent' => [
            'App\Listeners\WorkflowRejecListener',
        ],
        'App\Events\SaleOrderOutPassEvent' => [
            'App\Listeners\SaleOrderOutPassListener',
        ],
        'App\Events\SaleOrderOutRejectEvent' => [
            'App\Listeners\SaleOrderOutRejectListener',
        ],
        'App\Events\ReturnSaleOrderPassEvent' => [
            'App\Listeners\ReturnSaleOrderPassListener',
        ],
        'App\Events\ReturnSaleOrderRejectEvent' => [
            'App\Listeners\ReturnSaleOrderRejectListener',
        ],
        'App\Events\ReturnSaleOrderInPassEvent' => [
            'App\Listeners\ReturnSaleOrderInPassListener',
        ],
        'App\Events\ReturnSaleOrderInRejectEvent' => [
            'App\Listeners\ReturnSaleOrderInRejectListener',
        ],
        'App\Events\LeaveRejectEvent' => [
            'App\Listeners\LeaveRejectListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
