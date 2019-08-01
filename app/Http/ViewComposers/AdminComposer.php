<?php

namespace App\Http\ViewComposers;

use App\Services\Common\AdminService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminComposer
{
    private $data = null;

    public function __construct(Request $request)
    {
        $this->data = new AdminService($request);
    }

    public function compose(View $view)
    {
        $view->with([
            'website_name'                => $this->data->website_name,
            'menu'                => $this->data->menu,
            'swiftFlows'          => $this->data->flow,
            'debugInfo'           => $this->data->debugInfo,
            'workflowDebugStatus' => $this->data->workflowDebugStatus,
        ]);
    }
}
