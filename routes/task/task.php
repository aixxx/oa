<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //任务 start
        $api->group(['prefix' => '/task'], function($api){
            $api->any('/task_detail',['as'=>'task.task_detail','uses'=> 'TaskController@task_detail']);
            $api->post('/is_accept',['as'=>'task.is_accept','uses'=> 'TaskController@is_accept']);
            $api->post('/create',['as'=>'task.create','uses'=> 'TaskController@create']);
            $api->any('/task_list',['as'=>'task.task_list','uses'=> 'TaskController@task_list']);
            $api->post('/handle',['as'=>'task.handle','uses'=> 'TaskController@handle']);
            $api->post('/task_status',['as'=>'task.task_status','uses'=> 'TaskController@task_status']);
            $api->post('/get_type',['as'=>'task.get_type','uses'=> 'TaskController@get_type']);
            $api->any('/search_list',['as'=>'task.search_list','uses'=> 'TaskController@search_list']);
            $api->post('/avg_score',['as'=>'task.avg_score','uses'=> 'TaskController@avg_score']);

            //催办
            $api->get('/urge_save','TaskController@urgeSave')->name('task.urge_save');
            $api->get('/urge_save_byid','TaskController@urgeSaveById')->name('task.urge_save_byid');

            //申诉
            $api->post('/score_appeal','TaskController@scoreAppeal')->name('task.score_appeal');
            //申诉列表
            $api->get('/score_appeal_list_by_userid','TaskController@scoreAppealListByUserId')->name('task.score_appeal_list_by_userid');
            //我的任务 查看平均分来源
            $api->get('/task_avg_score_by_month','TaskController@taskAvgScoreByMonth')->name('task.task_avg_score_by_month');
            //点击待办的通知事项， 改为已读
            $api->get('/change_read_status', 'TaskController@changeReadStatus')->name('task.change_read_status');
        });
    });


});
