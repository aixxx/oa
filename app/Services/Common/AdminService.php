<?php

namespace App\Services\Common;

use App\Models\Basic\BasicSet;
use Auth;
use Session;
use Illuminate\Http\Request;
use App\Models\DepartUser;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Entry;
use App\Models\User;

class AdminService
{
    public $menu                = null;//菜单对象
    public $flow                = null;//快捷菜单对应的工作流
    public $debugInfo           = null;//工作流调试对应的信息
    public $workflowDebugStatus = false;//工作流调试状态,true为打开，false为关闭
    public $website_name       ="后台管理系统";

    public function __construct(Request $request)
    {
        $this->init($request);
    }

    private function init(Request $request)
    {
        $this->fetchMenu($request);
        $this->fetchDebugInfo($request);
        $this->fetchWorkflowDebugStatus($request);
        $this->basicInfo($request);
    }
    private function basicInfo(Request $request){
        $website_name = BasicSet::query()->value('website_name');
        $this->website_name = $website_name?$website_name:'后台管理系统';
    }

    private function fetchMenu(Request $request)
    {
        $records = auth()->user()->getAbilities();
        $level1  = array_unique($records->pluck('level1_no', 'id')->toArray());//一级菜单
        $level2  = array_unique($records->pluck('level2_no', 'id')->toArray());//二级菜单
        $level3  = array_unique($records->pluck('level3_no', 'id')->toArray());//三级菜单
        $constant = config('constant');
        $menu     = $constant['menu'];
        $menuList = '';
        if (!empty($menu)) {
            foreach ($menu as $m1) {
                $sideMenuList = '';
                if (in_array($m1['no'], $level1)) {
                    //临时修改侧边展示设置选项
                    if ($m1['no'] != 5) {
                        $sideMenuList .= '<li class="sidebar-header"><span>' . $m1['title'] . '</span></li>';
                    }
                }
                $contentList = '';
                if (isset($m1['children'])) {
                    foreach ($m1['children'] as $m2) {
                        $count = isset($m2['children']) ? count($m2['children']) : 0;

                        $header = $count > 1 ? '<li class="nav-dropdown">' : '<li>';
                        if (in_array($m2['no'], $level2)) {
                            $url    = isset($m2['default_url']) ? url('/' . $m2['default_url']) : '#';
                            $header .= $count > 1 ? '<a class="has-arrow" href="' . $url .
                                '" aria-expanded="false"><i class="' . $m2['icon'] .
                                '"></i><span>' .
                                $m2['title'] . '</span></a>' : '<a href="' . $url .
                                '" aria-expanded="false"><i class="' . $m2['icon'] .
                                '"></i><span>' .
                                $m2['title'] . '</span></a>';
                        }

                        $content = '';

                        if ($count > 1) {
                            foreach ($m2['children'] as $key => $m3) {
                                if (isset($m3['no']) && isset($m3['default_url']) && in_array($m3['no'], $level3)) {
                                    if (('attendance/department_leader' == $m3['default_url']) && !(DepartUser::checkUserIsLeader(Auth::id()))) {
                                        continue;
                                    }
                                    $content .= '<li><a href="' . url('/' . $m3['default_url']) .
                                        '"><span>' . $m3['title'] . '</span></a></li>';
                                }
                            }
                        }

                        //隐藏侧边栏员工设置选择
                        if (!isset($m2['children']['users_setting'])) {
                            $contentList .= $count > 1 ?
                                $header . '<ul class="collapse nav-sub" aria-expanded="false">' . $content .
                                '</ul></li>' : $header . '</li>';
                        }
                    }
                }
                $menuList .= $sideMenuList;
                $menuList .= $contentList;
            }
        }
        $this->menu = $menuList;
    }

    private function fetchDebugInfo(Request $request)
    {
        $configApp = config('app');
        $roles     = Auth()->user()->getRoles()->toArray();
        $result    = ['loginName' => '', 'leaderName' => ''];
        if ($configApp['debug'] && in_array('test_workflow', $roles)) {
            $result['loginName']  = Auth()->user()->chinese_name;
            $leadId               = Session::get('grant_user_' . Auth()->user()->id);
            $result['leaderName'] = !empty($session) ? User::find($leadId)->chinese_name : '';
        }

        $this->debugInfo = $result;
    }

    private function fetchWorkflowDebugStatus(Request $request)
    {
        $configApp = config('app');
        $roles     = Auth()->user()->getRoles()->toArray();
        if ($configApp['debug'] && in_array('test_workflow', $roles)) {
            $this->workflowDebugStatus = true;
        }
    }
}
