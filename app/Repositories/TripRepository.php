<?php

namespace App\Repositories;

use App\Models\Trip\Trip;
use Exception;
use App\Constant\ConstFile;
use App\Repositories\UsersRepository;
use DB;

class TripRepository extends Repository
{

    public function model()
    {
        return Trip::class;
    }

    public function getfiled($company_id)
    {
        $re = DB::table('addwork_company')
            ->leftJoin('addwork_field', 'addwork_company.field_id', '=', 'addwork_field.id')
            ->where('addwork_company.company_id', '=', $company_id)
            ->where('addwork_company.type', '=', 1)
            ->where('addwork_field.p_id', '=', 1)
            ->where('addwork_field.status', '=', 1)
            ->where('addwork_field.type', '<>', 'def')
            ->get(['addwork_field.id', 'addwork_field.e_name']);
        $result = array_map('get_object_vars', $re->toArray());
        foreach ($result as $k => $v) {
            $work = DB::table('addwork_field')->where('p_id', $v['id'])->get(['id', 'e_name']);
            if ($work) {
                $ae = array_map('get_object_vars', $work->toArray());
                $result[$k]['s_name'] = $ae;
                foreach ($result[$k]['s_name'] as $key => $value) {
                    $ten = DB::table('addwork_field')->where('p_id', $value['id'])->pluck('e_name', 'id');
                    if ($ten) {
                        $result[$k]['s_name'][$key]['t_name'] = $ten;
                    }
                }
            }
        }
        return returnJson($message = 'ok', $code = '200', $data = $result);
    }


    public function create_trip($data, $userid, $userinfo)
    {
        try {
            DB::transaction(function () use ($data, $userid, $userinfo) {
                $u_name = DB::table('users')->where('id', $userid)->value('name');  //当前用户


                $dept = app()->make(UsersRepository::class);
                $dept_arr = $dept->getCurrentDept($userinfo);

//                $str = '';
//                foreach ($dept_arr as $a => $b) {
//                    $str .= $b . ',';
//                }
//                $dept_str = rtrim($str, ',');        // 技术部

                $position = DB::table('users')->where('id', $userid)->value('position');  //当前用户

                $res['userid'] = $userid;
                $res['uname'] = $u_name;
                $res['dept'] = $dept_arr['name'];
                $res['position'] = $position;
                $res['trip_number'] = 123;
                $res['status'] = 0;                 //待审核状态

                //获取后台选择的字段
                $userRespository = app()->make(UsersRepository::class);
                $company = $userRespository->getAllDept('', $userinfo);
                $company_id = $company['id'];
                $re = DB::table('addwork_company')
                    ->leftJoin('addwork_field', 'addwork_company.field_id', '=', 'addwork_field.id')
                    ->where('addwork_company.company_id', '=', $company_id)
                    ->where('addwork_field.status', '=', 1)
                    ->where('addwork_field.p_id', '=', 1)
                    ->where('addwork_field.type', '<>', 'def')
                    ->get();
                $relt = array_map('get_object_vars', $re->toArray());


                foreach ($relt as $v) {
                    switch ($v['e_name']) {
                        case 'trip_count':    //出差天数
                            // 统计附表的“时长统计”
                            $res['trip_count'] = $data['trip_count'];
                            break;
                        case 'trip_info':           //出差备注
                            $res['trip_info'] = $data['trip_info'];
                            break;
                        case 'cause':               // 出差事由
                            $res['cause'] = $data['cause'];
                            break;
                        case 'together_person':     //同行人
                            // 获取职位
                            $cut = explode(',', $data['together_person']);
                            $toge = '';
                            foreach ($cut as $key => $value) {
                                $person = DB::table('users')->where('id', $value)->value('name');
                                $toge .= $person . ',';
                            }
                            $res['together_person'] = rtrim($toge, ',');  //超级管理员,徐卫松 同行人
                            break;
                        case 'created_at':          //申请时间
                            $res['created_at'] = $data['created_at'] ? $data['created_at'] : date('Y-m-d H:i:s', time());
                            break;
                    }
                }
                $trip_add_id = DB::table('trip')->insertGetId($res);


                //获取副表字段和前端传过来的对比，然后写入
                foreach ($relt as $a) {
                    switch ($a['e_name']) {
                        case 'trip':                    //行程
                            $f_id = DB::table('addwork_field')->where('e_name', 'trip')->value('id');
                            $info = DB::table('addwork_field')->where('p_id', $f_id)->get();
                            $resu = array_map('get_object_vars', $info->toArray());
                            $add = [];
                            foreach ($resu as $k => $val) {
                                foreach ($data['trip'][0][0] as $va => $vb) {      //前端传过来的trip数组
                                    switch ($val['e_name']) {
                                        case 'vehicle':
                                            $add[$va]['vehicle'] = $vb['vehicle'];
                                            break;
                                        case 'go_type':
                                            $add[$va]['go_type'] = $vb['go_type'];
                                            break;
                                        case 'depart_city':
                                            $add[$va]['depart_city'] = $vb['depart_city'];
                                            break;
                                        case 'whither_city':
                                            $add[$va]['whither_city'] = $vb['whither_city'];
                                            break;
                                        case 'begin_time':
                                            $add[$va]['begin_time'] = $vb['begin_time'];
                                            break;
                                        case 'end_time':
                                            $add[$va]['end_time'] = $vb['end_time'];
                                            break;
                                        case 'time_count':
                                            $add[$va]['time_count'] = $vb['time_count'];
                                            break;
                                    }
                                    $add[$va]['trip_id'] = $trip_add_id;
                                }
                            }
                            DB::table('trip_agenda')->insert($add);
                            break;
                        case 'copy_person':                 //抄送人
                            if ($data['copy_person_ids']) {
                                $copypersonArray = $data['copy_person_ids'];
                            } else {
                                $copypersonArray = '';
                            }
                            $copy_arr = explode(',', $copypersonArray);
                            foreach ($copy_arr as $ka => $kb) {
                                $uname = DB::table('users')->where('id', $kb)->value('name');
                                if (!$uname) {
                                    return returnJson($message = '抄送人不存在', $code = '1002');
                                }
                                $mat['tid'] = $trip_add_id;
                                $mat['uid'] = $kb;
                                $mat['type_name'] = $uname;
                                $mat['user_type'] = 2;  //抄送人类型
                                $mat['create_user_id'] = $userid;
                                DB::table('trip_user')->insert($mat);
                            }
                            break;
                    }
                }
            });
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
            return $result;
        }
        return returnJson($message = '创建成功', $code = '200');
    }


    function assoc_unique($arr, $key)
    {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)){
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }


    //差审核状态，得现有审核信息
    public function trip_list($userid, $info)
    {

        $re = DB::table('trip')
            ->leftJoin('trip_agenda', 'trip.id', '=', 'trip_agenda.trip_id')
            ->where('trip.userid', $userid)
            ->skip(($info['page'] - 1) * $info['limit'])->take($info['limit'])
            ->get(['trip.id', 'trip.status', 'trip.uname', 'trip.created_at', 'trip.cause', 'trip.trip_count', 'trip_agenda.vehicle']);

        $rett = array_map('get_object_vars', $re->toArray());

        $ret = $this->assoc_unique($rett,'id');

        foreach ($ret as $k => $v) {
            switch ($v['status']) {
                case '1':
                    $ret[$k]['audit_info'] = '审批通过';
                    break;
                case '-1':
                    $ret[$k]['audit_info'] = '审批拒绝';
                    break;
                case '0':
                    $ret[$k]['audit_info'] = '待审批';
                    break;
            }
            switch ($v['vehicle']) {
                case '1':
                    $ret[$k]['vehicle'] = '飞机';
                    break;
                case '2':
                    $ret[$k]['vehicle'] = '火车';
                    break;
                case '3':
                    $ret[$k]['vehicle'] = '汽车';
                    break;
                case '4':
                    $ret[$k]['vehicle'] = '其他';
                    break;
            }
        }
        return returnJson($message = '获取成功', $code = '200', $data = $ret);
    }


    public function detail($info, $userid)
    {
        $where['id'] = $info['trip_id'];
        $where['userid'] = $userid;
        $res = DB::table('trip')->where($where)->get();
        $ret = array_map('get_object_vars', $res->toArray());
//        print_r($res);die;
        $mat = [];
        foreach ($ret as $k => $v) {
            $rat = DB::table('trip_agenda')->where('trip_id', $v['id'])->get();
            $rats = array_map('get_object_vars', $rat->toArray());

            //详情页主体 和 行程
            foreach ($rats as $key => $val) {
                $mat['id'] = $v['id'];
                $mat['trip_number'] = $v['trip_number'];
                $mat['userid'] = $v['userid'];
                $mat['uname'] = $v['uname'];
                $mat['dept'] = $v['dept'];
                $mat['cause'] = $v['cause'];
                $mat['trip_count'] = $v['trip_count'];
                $mat['together_person'] = $v['together_person'];

                $mat['xingcheng'][$key]['vehicle'] = $val['vehicle'];
                $mat['xingcheng'][$key]['go_type'] = $val['go_type'];
                $mat['xingcheng'][$key]['depart_city'] = $val['depart_city'];
                $mat['xingcheng'][$key]['whither_city'] = $val['whither_city'];
                $mat['xingcheng'][$key]['begin_time'] = $val['begin_time'];
                $mat['xingcheng'][$key]['end_time'] = $val['end_time'];
                $mat['xingcheng'][$key]['time_count'] = $val['time_count'];
            }

            //抄送人
            $set = DB::table('trip_user')->where('tid', $v['id'])->where('create_user_id', $v['userid'])->where('user_type', 2)->get();
            $sets = array_map('get_object_vars', $set->toArray());
            $str = '';
            foreach ($sets as $kk => $vv) {
                $str .= $vv['type_name'] . ',';
                $mat['copy_person'] = rtrim($str, ',');
            }

            //审批人
            $seta = DB::table('total_audit')->where('relation_id', $v['id'])
                ->where('create_user_id', $v['userid'])
                ->where('type', 1)
                ->get();
            $setas = array_map('get_object_vars', $seta->toArray());
            foreach ($setas as $keys => $value) {
                $mat['audit'][$keys]['uid'] = $value['uid'];
                $mat['audit'][$keys]['user_name'] = $value['user_name'];
                switch ($value['status']) {
                    case -1:
                        $mat['audit'][$keys]['status'] = '拒绝';
                        break;
                    case 1:
                        $mat['audit'][$keys]['status'] = '同意';
                        break;
                }
                $mat['audit'][$keys]['audit_info'] = $value['audit_info'];
                $mat['audit'][$keys]['audit_img'] = $value['audit_img'];
                $mat['audit'][$keys]['audit_field'] = $value['audit_field'];
            }

            //评论
            $comme = DB::table('total_comment')->where('relation_id', $v['id'])->where('type', 1)->get();
            $comment = array_map('get_object_vars', $comme->toArray());
            foreach ($comment as $ki => $vi) {
                $mat['comment'][$ki]['uid'] = $vi['uid'];
                $mat['comment'][$ki]['user_name'] = $vi['user_name'];
                $mat['comment'][$ki]['comment_text'] = $vi['comment_text'];
                $mat['comment'][$ki]['comment_img'] = $vi['comment_img'];
                $mat['comment'][$ki]['comment_field'] = $vi['comment_field'];
                $mat['comment'][$ki]['comment_time'] = $vi['comment_time'];
            }
        }
        return returnJson($message = '获取成功', $code = '200', $data = $mat);
    }

    //评论
    public function comment($info, $userid)
    {
        $coms = [];
        if ($info['type'] == 1) {                 //审核人评论
            $set['tid'] = $info['trip_id'];
            $set['uid'] = $userid;
            $set['user_type'] = 1;
            $com = DB::table('trip_user')->where($set)->get();
            if (!$com) {
                return returnJson($message = '用户类型不符合或数据不存在', $code = '1001');
            }
            $coms = array_map('get_object_vars', $com->toArray());
        }

        if ($info['type'] == 2) {             //抄送人评论
            $where['relation_id'] = $info['trip_id'];
            $where['type'] = 1;
            $where['is_success'] = 1;
            $cm = DB::table('total_audit')->where($where)->get();
            if (!$cm) {
                return returnJson($message = '未完成的出差，抄送人暂不可评论', $code = '1002');
            }

            $cet['tid'] = $info['trip_id'];
            $cet['uid'] = $userid;
            $cet['user_type'] = 2;
            $com = DB::table('trip_user')->where($cet)->get();
            if (!$com) {
                return returnJson($message = '数据不存在', $code = '1003');
            }
            $coms = array_map('get_object_vars', $com->toArray());
        }

        if (!$info['comment_text']) {
            return returnJson($message = '评论内容不能为空！', $code = '1004');
        }

        $res = [];
        foreach ($coms as $k => $v) {
            $res[$k]['type'] = 1;
            $res[$k]['relation_id'] = $v['tid'];
            $res[$k]['uid'] = $v['uid'];
            $res[$k]['comment_text'] = $info['comment_text'];
            $res[$k]['comment_img'] = $info['comment_img'];
            $res[$k]['comment_field'] = $info['comment_field'];
            $res[$k]['comment_time'] = date('Y-m-d H:i:s', time());
        }
        $met = DB::table('total_comment')->insert($res);
        if ($met) {
            return returnJson($message = '评论成功', $code = '200');
        }

    }

    /**
     * 二维数组根据某个字段排序
     * @param array $array 要排序的数组
     * @param string $keys 要排序的键字段
     * @param string $sort 排序类型  SORT_ASC     SORT_DESC
     * @return array 排序后的数组
     */
    function arraySort($array, $keys, $sort = SORT_DESC)
    {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }

    //审核
    public function audit($info, $userid)
    {

        //查询是否审批过
        $tac['uid'] = $userid;
        $tac['relation_id'] = $info['trip_id'];
        $tac['type'] = 1;

        $sit = DB::table('total_audit')->where($tac)->get();
        $cos = array_map('get_object_vars', $sit->toArray());
        if ($cos) {
            return returnJson($message = '已审核过，不能重复审批', $code = '1001');
        }

        //查审批的数组倒序
        $ss['tid'] = $info['trip_id'];
        $ss['user_type'] = 1;
        $sin = DB::table('trip_user')->where($ss)->get();
        $cos = array_map('get_object_vars', $sin->toArray());
        $da = $this->arraySort($cos, 'level', SORT_DESC);

        //查当前审批的人
        $set['tid'] = $info['trip_id'];
        $set['uid'] = $userid;
        $set['user_type'] = 1;
        $mat = DB::table('trip_user')->where($set)->get();
        if (!$mat) {
            return returnJson($message = '数据有误', $code = '1002');
        }
        $coms = array_map('get_object_vars', $mat->toArray());
        $cat = [];
        foreach ($da as $a => $b) {
            foreach ($coms as $k => $v) {
                $cat[$k]['type'] = 1;
                $cat[$k]['relation_id'] = $v['tid'];
                $cat[$k]['uid'] = $v['uid'];
                $cat[$k]['user_name'] = $v['type_name'];
                $cat[$k]['status'] = $info['audit'];
                $cat[$k]['audit_time'] = date('Y-m-d H:i:s', time());
                $cat[$k]['create_user_id'] = $v['create_user_id'];
                //看当前任务是否是最后一次
                if ($v['uid'] == $da[0]['uid']) {
                    if (!empty($info['audit'])) {
                        $cat[$k]['is_success'] = 1;
                        DB::table('trip')->where('id', $info['trip_id'])->update(array('status' => $info['audit']));
                    }
                } else {
                    if (!empty($info['audit'])) {
                        $cat[$k]['is_success'] = 0;
                    }
                }
            }
        }
        $met = DB::table('total_audit')->insert($cat);
        if ($met) {
            return returnJson($message = '审核完成', $code = '200');
        }

    }


}
