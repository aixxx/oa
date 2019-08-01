@extends("layouts.main",['title' => '员工'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">员工信息</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">员工信息</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header font-size-20">基础信息
                <a href="{{  route('users.edit',['id' => $user->id,'type' => 'basic'])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right" style="margin-left: 10px;">编辑</a>
                @if($user->status != 9)
                    <a href="javascript:void(0)" class="btn btn-accent btn-sm line-height-fix float-right simple_del"
                       data-userid="{{  $user->id  }}">离职</a>
                @endif&nbsp;
            </h3>
            <div class="card-body font-size-fix">
                <div class="row">
                    @if($user->name)
                        <div class="col-md-6 text-left">系统唯一账号：{{  $user->name  }}</div>
                    @else
                        <div class="col-md-6 text-left">系统唯一账号：</div>
                    @endif
                    @if($user->status == 9)
                        <div class="col-md-6">员工状态：离职
                            @if(isset($user->dimission) && $user->dimission)
                                <a href="{{  route('dimission.show',['id' => $user->dimission->id]) }}" target="_blank"
                                   class="btn btn-secondary btn-sm line-height-fix">离职信息</a>
                            @else
                                <a href="{{  route('dimission.create',['id' => $user->id]) }}" target="_blank"
                                   class="btn btn-secondary btn-sm line-height-fix">添加离职信息</a>
                            @endif
                        </div>
                    @else
                        <div class="col-md-6">员工状态：在职</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 text-left">中文名： {{  $user->chinese_name  }}</div>
                    <div class="col-md-6">英文名： {{  $user->english_name  }} </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 text-left">员工编号： {{  $user->employee_num  }}</div>
                    <div class="col-md-6">所属公司： {{  rtrim($user->company_name ,';') }}</div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-6 text-left" id="dept">所属部门：
                        @php echo rtrim($user->depart_name,';');  @endphp
                    </div>
                    <div class="col-md-6">主部门：@php echo $user->pri_depart;  @endphp</div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 text-left">email：{{  $user->email  }}</div>
                    @if($user->mobile)
                        <div class="col-md-6">手机号：{{  decrypt_no_user_exception($user->mobile)  }}</div>
                    @else
                        <div class="col-md-6">手机号：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    <div class="col-md-3 text-left">职位： {{  $user->position  }}</div>
                    <div class="col-md-3"></div>

                    <div class="col-md-3">性别： <?php  echo ($user->gender == \App\Models\User::GENDER_UNKNOWN) ? '未知' :
                            ($user->gender == \App\Models\User::GENDER_MALE ? '男' : '女'); ?></div>

                    <div class="col-md-3"></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 text-left">固定电话： {{  $user->telephone  }}</div>
                    @if($user->isleader == 1)
                        <div class="col-md-6">高管：是</div>
                    @else
                        <div class="col-md-6">高管：否</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->is_sync_wechat == 0)
                        <div class="col-md-6 text-left">是否同步：否</div>
                    @else
                        <div class="col-md-6 text-left">是否同步：是</div>
                    @endif

                    @if($user->join_at)
                        <div class="col-md-6">入职时间：{{  date('Y-m-d',strtotime($user->join_at))  }}</div>
                    @else
                        <div class="col-md-6">入职时间：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->leave_at)
                        <div class="col-md-6 text-left">离职时间：{{  date('Y-m-d',strtotime($user->leave_at))  }}</div>
                    @else
                        <div class="col-md-6">离职时间：</div>
                    @endif

                    @if($user->work_address)
                        @if($user->work_address == 'shanghai')
                            <div class="col-md-6">工作地点：上海</div>
                        @elseif($user->work_address == 'beijing')
                            <div class="col-md-6">工作地点：北京</div>
                        @elseif($user->work_address == 'chengdu')
                            <div class="col-md-6">工作地点：成都</div>
                        @elseif($user->work_address == 'shenzhen')
                            <div class="col-md-6">工作地点：深圳</div>
                        @endif
                    @else
                        <div class="col-md-6">工作地点：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if(isset(\App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE[$user->work_type]))
                        <div class="col-md-6 text-left">班值类型： {{  isset(\App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE[$user->work_type]) ? \App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE[$user->work_type] : ''  }}</div>
                    @else
                        <div class="col-md-6 text-left"></div>
                    @endif

                    @if($user->work_title && $user->workClass)
                        <div class="col-md-6">班值代码：{{ $user->workClass ? $user->workClass->class_name : ''}}</div>
                    @else
                        <div class="col-md-6"></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">工作信息
                <a href="{{  route('users.edit',['id' => $user->id,'type' => 'job'])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>
            </h3>
            <div class="card-body font-size-fix">
                <div class="row">
                    @if($user->detail  && $user->detail->user_type)
                        @if(decrypt_no_user_exception($user->detail->user_type) == 'full-time')
                            <div class="col-md-6 text-left">员工类型：全职</div>
                        @else
                            <div class="col-md-6 text-left">员工类型：兼职</div>
                        @endif
                    @else
                        <div class="col-md-6 text-left">员工类型：</div>
                    @endif


                    @if($user->detail && $user->detail->user_status)
                        @if(decrypt_no_user_exception($user->detail->user_status) == 'regular')
                            <div class="col-md-6">员工状态：正式</div>
                        @else
                            <div class="col-md-6">员工状态：非正式</div>
                        @endif
                    @else
                        <div class="col-md-6">员工状态：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->probation)
                        <div class="col-md-6 text-left">试用期： {{  decrypt_no_user_exception($user->detail->probation)  }}</div>
                    @else
                        <div class="col-md-6 text-left">试用期：</div>
                    @endif

                    @if($user->regular_at)
                        <div class="col-md-6">转正日期： {{  strtotime($user->regular_at )>0 ? date('Y-m-d',strtotime($user->regular_at )):'' }}</div>
                    @else
                        <div class="col-md-6">转正日期：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->grade)
                        <div class="col-md-6 text-left">岗位职级： {{  decrypt_no_user_exception($user->detail->grade)  }}</div>
                    @else
                        <div class="col-md-6 text-left">岗位职级：</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">身份信息
                <a href="{{  route('users.edit',['id' => $user->id,'type' => 'person'])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>
            </h3>
            <div class="card-body font-size-fix">
                <div class="row">
                    @if($user->detail && $user->detail->id_name)
                        <div class="col-md-6 text-left">身份证姓名： {{  decrypt_no_user_exception($user->detail->id_name)  }}</div>
                    @else
                        <div class="col-md-6 text-left">身份证姓名：</div>
                    @endif

                    @if($user->detail && $user->detail->id_number)
                        <div class="col-md-6">证件号码： {{  decrypt_no_user_exception($user->detail->id_number)  }}</div>
                    @else
                        <div class="col-md-6">证件号码：</div>
                    @endif

                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->born_time)
                        <div class="col-md-6 text-left">出生日期： {{  decrypt_no_user_exception($user->detail->born_time)  }}</div>
                    @else
                        <div class="col-md-6 text-left">出生日期：</div>
                    @endif

                    @if($user->detail && $user->detail->ethnic)
                        <div class="col-md-6">民族： {{  decrypt_no_user_exception($user->detail->ethnic)  }}</div>
                    @else
                        <div class="col-md-6">民族：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->id_address)
                        <div class="col-md-6 text-left">身份证地址：{{  decrypt_no_user_exception($user->detail->id_address)  }}</div>
                    @else
                        <div class="col-md-6 text-left">身份证地址：</div>
                    @endif

                    @if($user->detail && $user->detail->validity_certificate)
                        <div class="col-md-6">证件有效期：{{  decrypt_no_user_exception($user->detail->validity_certificate)  }}</div>
                    @else
                        <div class="col-md-6">证件有效期：</div>
                    @endif

                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->census_type)
                        @if(decrypt_no_user_exception($user->detail->census_type) == 'agriculture')
                            <div class="col-md-6 text-left">户籍类型： 农业</div>
                        @else
                            <div class="col-md-6 text-left">户籍类型： 非农业</div>
                        @endif
                    @else
                        <div class="col-md-6 text-left">户籍类型：</div>
                    @endif

                    @if($user->detail && $user->detail->address)
                        <div class="col-md-6">住址：{{  decrypt_no_user_exception($user->detail->address)  }}</div>
                    @else
                        <div class="col-md-6">住址：</div>
                    @endif

                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->politics_status)
                        @if(decrypt_no_user_exception($user->detail->politics_status) == 'party_member')
                            <div class="col-md-6 text-left">政治面貌：党员</div>
                        @else
                            <div class="col-md-6 text-left">政治面貌：群众</div>
                        @endif
                    @else
                        <div class="col-md-6 text-left">政治面貌：</div>
                    @endif

                    @if($user->detail  &&  $user->detail->marital_status)
                        @if(decrypt_no_user_exception($user->detail->marital_status) == 'unmarried')
                            <div class="col-md-6">婚姻状况：未婚</div>
                        @elseif(decrypt_no_user_exception($user->detail->marital_status) == 'married')
                            <div class="col-md-6">婚姻状况：已婚</div>
                        @elseif(decrypt_no_user_exception($user->detail->marital_status) == 'divorced')
                            <div class="col-md-6">婚姻状况：离异</div>
                        @else
                            <div class="col-md-6">婚姻状况：丧偶</div>
                        @endif
                    @else
                        <div class="col-md-6">婚姻状况：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->first_work_time)
                        <div class="col-md-6 text-left">首次参加工作时间：{{  $user->detail->first_work_time  }}</div>
                    @else
                        <div class="col-md-6 text-left">首次参加工作时间：</div>
                    @endif

                    @if($user->detail && $user->detail->per_social_account)
                        <div class="col-md-6">个人社保账号：{{  decrypt_no_user_exception($user->detail->per_social_account)  }}</div>
                    @else
                        <div class="col-md-6">个人社保账号：</div>
                    @endif

                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->per_fund_account)
                        <div class="col-md-6 text-left">个人公积金账号： {{  decrypt_no_user_exception($user->detail->per_fund_account)  }}</div>
                    @else
                        <div class="col-md-6 text-left">个人公积金账号：</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">学历信息
                <a href="{{  route('users.edit',['id' => $user->id,'type' => 'edu'])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>
            </h3>
            <div class="card-body font-size-fix">
                <div class="row">
                    @if($user->detail && $user->detail->highest_education)
                        <div class="col-md-6 text-left">最高学历： {{  decrypt_no_user_exception($user->detail->highest_education)  }}</div>
                    @else
                        <div class="col-md-6 text-left">最高学历：</div>
                    @endif

                    @if($user->detail && $user->detail->graduate_institutions)
                        <div class="col-md-6">毕业院校：{{  decrypt_no_user_exception($user->detail->graduate_institutions)  }}</div>
                    @else
                        <div class="col-md-6">毕业院校：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->graduate_time)
                        <div class="col-md-6 text-left">毕业时间： {{  decrypt_no_user_exception($user->detail->graduate_time)  }}</div>
                    @else
                        <div class="col-md-6 text-left">毕业时间：</div>
                    @endif

                    @if($user->detail && $user->detail->major)
                        <div class="col-md-6">所学专业： {{  decrypt_no_user_exception($user->detail->major)  }}</div>
                    @else
                        <div class="col-md-6">所学专业：</div>
                    @endif

                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">银行卡信息
                <a href="{{  route('users.edit',['id' => $user->id,'type' => 'card'])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>
            </h3>
            <div class="card-body font-size-fix">
                @if($user->bankCard)
                    @foreach($user->bankCard as $value)
                        <div class="row">
                            <div class="col-md-3">类型：
                                @if($value->bank_type == \App\Models\UserBankCard::BANK_CARD_TYPE_MAIN)
                                    主卡
                                @elseif($value->bank_type == \App\Models\UserBankCard::BANK_CARD_TYPE_VICE)
                                    副卡
                                @endif
                            </div>
                            <br>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                银行卡号：@php echo $value['card_num'] ? \App\Models\UserBankCard::formatBankCardShowType(decrypt_no_user_exception($value['card_num'],$value['user_id']??0) ,4): ""; @endphp</div>
                            <div class="col-md-3">
                                开户行：@php echo $value['bank'] ? decrypt_no_user_exception($value['bank']) : ""; @endphp</div>
                            <div class="col-md-3">
                                支行名称：@php echo $value['branch_bank'] ? decrypt_no_user_exception($value['branch_bank']) : ""; @endphp</div>
                            <div class="col-md-3">
                                银行卡属地：
                                @php
                                    if ($value['bank_province'] && $value['bank_city']) {
                                        echo decrypt_no_user_exception($value['bank_province']).decrypt_no_user_exception($value['bank_city']);
                                    } elseif ($value['bank_province'] && !$value['bank_city']) {
                                        echo decrypt_no_user_exception($value['bank_province']);
                                    } elseif (!$value['bank_province'] && $value['bank_city']){
                                        echo decrypt_no_user_exception($value['bank_city']);
                                    } else {
                                        echo "";
                                    }
                                @endphp
                            </div>
                        </div>
                        <br>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">合同信息
                <a href="{{  route('users.edit',['id' => $user->id,'type' => 'contract'])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>
            </h3>
            <div class="card-body font-size-fix">
                <div class="row">
                    @if($user->detail && $user->detail->contract_company)
                        <div class="col-md-6 text-left">合同公司： {{  decrypt_no_user_exception($user->detail->contract_company)  }}</div>
                    @else
                        <div class="col-md-6 text-left">合同公司：</div>
                    @endif


                    @if($user->detail && $user->detail->contract_type)
                        @if(decrypt_no_user_exception($user->detail->contract_type) == 'fixed')
                            <div class="col-md-6">合同类型： 全职</div>
                        @else
                            <div class="col-md-6">合同类型： 兼职</div>
                        @endif
                    @else
                        <div class="col-md-6">合同类型：</div>
                    @endif

                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->first_contract_start_time)
                        <div class="col-md-6 text-left">
                            首次合同起始日： {{  decrypt_no_user_exception($user->detail->first_contract_start_time)  }}</div>
                    @else
                        <div class="col-md-6 text-left">首次合同起始日：</div>
                    @endif

                    @if($user->detail && $user->detail->first_contract_end_time)
                        <div class="col-md-6">首次合同到期日： {{  decrypt_no_user_exception($user->detail->first_contract_end_time)  }}</div>
                    @else
                        <div class="col-md-6">首次合同到期日：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->cur_contract_start_time)
                        <div class="col-md-6 text-left">
                            现合同起始日： {{  decrypt_no_user_exception($user->detail->cur_contract_start_time)  }}</div>
                    @else
                        <div class="col-md-6 text-left">现合同起始日：</div>
                    @endif

                    @if($user->detail && $user->detail->cur_contract_end_time)
                        <div class="col-md-6">现合同到期日：{{  decrypt_no_user_exception($user->detail->cur_contract_end_time)  }}</div>
                    @else
                        <div class="col-md-6">现合同到期日：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($user->detail && $user->detail->contract_term)
                        <div class="col-md-6 text-left">合同期限：{{  decrypt_no_user_exception($user->detail->contract_term)  }}年</div>
                    @else
                        <div class="col-md-6 text-left">合同期限：</div>
                    @endif

                    @if($user->detail && $user->detail->renew_times)
                        <div class="col-md-6">续签次数： {{  decrypt_no_user_exception($user->detail->renew_times)  }}次</div>
                    @else
                        <div class="col-md-6">续签次数：</div>
                    @endif

                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">紧急联系人
                {{--<a href="{{  route('users.edit',['id' => $user->id,'type' => 'emerge'])  }}" class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>--}}
            </h3>
            <div class="card-body font-size-fix">
                @if($user->urgentUser)
                    @foreach($user->urgentUser as $value)
                        <div class="row">
                            <div class="col-md-4">
                                关系：@php echo $value->relate ? decrypt_no_user_exception($value->relate) : "";  @endphp</div>
                            <div class="col-md-4">
                                姓名：@php echo $value->relate_name ? decrypt_no_user_exception($value->relate_name) : "";  @endphp</div>
                            <div class="col-md-4">
                                电话：@php echo $value->relate_phone ? decrypt_no_user_exception($value->relate_phone) : "";  @endphp</div>
                        </div>
                        <br>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">家庭信息
                {{--<a href="{{  route('users.edit',['id' => $user->id,'type' => 'family'])  }}" class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>--}}
            </h3>
            <div class="card-body font-size-fix">
                @if($user->family)
                    @foreach($user->family as $value)
                        <div class="row">
                            <div class="col-md-4">
                                关系：@php echo $value->family_relate ? decrypt_no_user_exception($value->family_relate) : "" @endphp</div>
                            <div class="col-md-4">
                                姓名：@php echo $value->family_name ? decrypt_no_user_exception($value->family_name) : "" @endphp</div>
                            <div class="col-md-4">性别：
                                @php
                                    if ($value->family_sex) {
                                        if (decrypt_no_user_exception($value->family_sex) == 1){
                                            echo  '男';
                                        } elseif(decrypt_no_user_exception($value->family_sex) == 2) {
                                            echo '女';
                                        } else {
                                            echo '未知';
                                        }
                                    }
                                @endphp
                            </div>
                        </div>
                        <br>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">个人材料
                <a href="{{  route('users.edit',['id' => $user->id,'type' => 'stuff'])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>
            </h3>
            <div class="card-body font-size-fix">
                <div class="row">
                    <div class="col-md-6 text-left">
                        <label for="">身份证（人像面）</label>
                        <br>
                        @if($user->detail)
                            <img src="{{  $user->detail->pic_id_pos  }}" class="img-thumbnail">
                        @else
                            <img src="" class="img-thumbnail">
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label>身份证（国徽面）</label>
                        <br>
                        @if($user->detail)
                            <img src="{{  $user->detail->pic_id_neg  }}" alt="" class="img-thumbnail">
                        @else
                            <img src="" alt="" class="img-thumbnail">
                        @endif

                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 text-left">
                        <label for="">学历证书</label>
                        <br>
                        @if($user->detail)
                            <img src="{{  $user->detail->pic_edu_background  }}" alt="" class="img-thumbnail">
                        @else
                            <img src="" alt="" class="img-thumbnail">
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label>学位证书</label>
                        <br>
                        @if($user->detail)
                            <img src="{{  $user->detail->pic_degree  }}" alt="" class="img-thumbnail">
                        @else
                            <img src="" alt="" class="img-thumbnail">
                        @endif
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 text-left">
                        <label for="">前公司离职证明</label>
                        <br>
                        @if($user->detail)
                            <img src="{{  $user->detail->pic_pre_company  }}" alt="" class="img-thumbnail">
                        @else
                            <img src="" alt="" class="img-thumbnail">
                        @endif

                    </div>
                    <br>
                    <div class="col-md-6">
                        <label>员工照片</label>
                        <br>
                        @if($user->detail)
                            <img src="{{  $user->detail->pic_user  }}" alt="" class="img-thumbnail">
                        @else
                            <img src="" alt="" class="img-thumbnail">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header font-size-20">员工信息变更记录</h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>操作人</th>
                        <th>目标人</th>
                        <th>动作</th>
                        <th>变更前</th>
                        <th>变更后</th>
                        <th class="text-center">发生时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($userChangeInfo as $key => $value)
                        <tr>
                            <td>{{  $value->operate_user_name  }}</td>
                            <td>{{  $value->target_user_name  }}</td>
                            <td>{{  $value->action_name  }}</td>
                            <td style="width: 20em;white-space:pre-wrap;">@php echo $value->init_messages;  @endphp</td>
                            <td style="width: 20em;white-space:pre-wrap;">@php echo $value->target_messages;  @endphp</td>
                            <td class="text-center">{{  $value->created_at  }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="delUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">提示</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <p>是否离职所选员工</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消
                    </button>
                    <button type="button" class="btn btn-danger btn-sm line-height-fix" id="del_user">确定</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection

@section('javascript')
    <script>
        update_success = "{{  session('updateSuccess')  }}";
        if (update_success) {
            alert(update_success);
        }

        edit_error = "{{  session('editError')  }}";

        if (edit_error) {
            alert(edit_error);
        }

    </script>
    <script>
        //删除单个员工
        simple_del = $(".simple_del");
        simple_del.off('click').click(function () {
            simple_del_id = $(this).data('userid');
            if (simple_del_id) {
                $("#delUserModal").modal('show');
                let del_user = $("#del_user");
                del_user.click(function () {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        dataType: "json",
                        url: "{{  route('users.delete')  }}",
                        data: {'ids': simple_del_id},
                        success: function (response) {
                            if (response.status == 'success') {
                                $("#delUserModal").modal('hide');
                                alert(response.messages);

                                window.location.href = "/users/" + simple_del_id + "/dimission_create";
                                // window.location.reload();
                            } else if (response.status == 'failed') {
                                alert(response.messages);
                            }
                        }
                    })
                })
            }
        });

        //员工筛选
        user_search = $("#search");
        user_search.click(function () {
            $("#searchModal").modal('show');
        });
    </script>
@endsection