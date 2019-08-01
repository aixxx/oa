@extends("layouts.main",['title' => '员工信息'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">个人信息</h1>
            </div>
        </div>
    </header>
    <style>
        #base-info-tab, #bank-card-info-tab, #user-urgent-contact-tab, #family-info-tab, #upload-real-photo-tab {
            border-radius: unset;
        }
    </style>
    <div class="content">
        <section class="page-content container-fluid">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="profile-card text-center">
                                <div class="thumb-xl member-thumb m-b-10 center-block">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" width="200" class="rounded-circle img-thumbnail"
                                             alt="profile-image">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1487530811176-3780de880c2d?fit=crop&w=500&q=80"
                                             width="200"
                                             class="rounded-circle img-thumbnail"
                                             alt="profile-image">
                                    @endif
                                </div>
                                <div class="">
                                    <h5 class="m-b-5">{{ $user->chinese_name }}</h5>
                                    @if($user->english_name)
                                        <p class="text-muted"><span>@</span>{{ $user->english_name }}</p>
                                    @endif
                                </div>
                                <ul class="list-reset text-left m-t-40">
                                    <li class="text-muted"><strong>Mobile:</strong><span
                                                class="m-l-15">{{ decrypt_no_user_exception($user->mobile) }}</span>
                                    </li>
                                    <li class="text-muted"><strong>Email:</strong> <span
                                                class="m-l-15">{{ $user->email }}</span></li>
                                    <li class="text-muted"><strong>Location:</strong> <span
                                                class="m-l-15">{{ $user->work_address }}</span></li>
                                </ul>
                            </div>
                        </div>
                        <hr/>
                        <div class="text-center">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                 aria-orientation="vertical">
                                <a class="nav-link active show" id="base-info-tab" data-toggle="pill" href="#base-info"
                                   role="tab" aria-controls="v-pills-home"
                                   aria-selected="true">基本信息</a>
                                <a class="nav-link" id="bank-card-info-tab" data-toggle="pill" href="#bank-card-info"
                                   role="tab" aria-controls="v-pills-profile"
                                   aria-selected="false">银行卡信息</a>
                                <a class="nav-link" id="user-urgent-contact-tab" data-toggle="pill"
                                   href="#user-urgent-contact" role="tab"
                                   aria-controls="v-pills-messages" aria-selected="false">紧急联系人</a>
                                <a class="nav-link" id="family-info-tab" data-toggle="pill" href="#family-info"
                                   role="tab" aria-controls="family-info"
                                   aria-selected="false">家庭信息</a>
                                <a class="nav-link" id="upload-real-photo-tab" data-toggle="pill"
                                   href="#upload-real-photo" role="tab" aria-controls="upload-real-photo"
                                   aria-selected="false">个人照片</a>
                                {{--<a class="nav-link" id="info-change-tab" data-toggle="pill" href="#info-change"--}}
                                   {{--role="tab" aria-controls="info-change" aria-selected="false">信息变更记录</a>--}}
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade active show" id="base-info" role="tabpanel"
                                 aria-labelledby="base-info-tab">
                                <h3 class="card-header font-size-20">基本信息</h3>
                                <div class="card-body font-size-fix">
                                    <div class="row">
                                        <div class="col-md-6 text-left">中文名： {{  $user->chinese_name  }}</div>
                                        <div class="col-md-6">英文名： {{  $user->english_name  }} </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        @if($user->name)
                                            <div class="col-md-6 text-left">账号：{{  $user->name  }}</div>
                                        @else
                                            <div class="col-md-6 text-left">账号：</div>
                                        @endif
                                        <div class="col-md-6 text-left">员工编号： {{  $user->getPrefixEmployeeNum() }}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">所属公司： {{  rtrim($user->company_name ,';') }}</div>
                                        <div class="col-md-6 text-left" id="dept">所属部门：
                                            @php echo rtrim($user->depart_name,';');  @endphp
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">主部门：@php echo $user->pri_depart;  @endphp</div>
                                        <div class="col-md-6 text-left">邮箱：{{  $user->email  }}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        @if($user->mobile)
                                            <div class="col-md-6">
                                                手机号：{{  decrypt_no_user_exception($user->mobile)  }}</div>
                                        @else
                                            <div class="col-md-6">手机号：</div>
                                        @endif
                                        <div class="col-md-6 text-left">职位： {{  $user->position  }}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            性别： <?php  echo ($user->gender == \App\Models\User::GENDER_UNKNOWN) ? '未知' :
                                                ($user->gender == \App\Models\User::GENDER_MALE ? '男' : '女'); ?></div>
                                        <div class="col-md-6 text-left">固定电话： {{  $user->telephone  }}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        @if($user->join_at)
                                            <div class="col-md-6">
                                                入职时间：{{  date('Y-m-d',strtotime($user->join_at))  }}</div>
                                        @else
                                            <div class="col-md-6">入职时间：</div>
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
                                            <div class="col-md-6 text-left">
                                                班值类型： {{  isset(\App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE[$user->work_type]) ? \App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE[$user->work_type] : ''  }}</div>
                                        @else
                                            <div class="col-md-6 text-left"></div>
                                        @endif
                                        @if($user->work_title && $user->workClass)
                                            <div class="col-md-6">
                                                班值代码：{{ $user->workClass ? $user->workClass->class_name : ''}}</div>
                                        @else
                                            <div class="col-md-6"></div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="bank-card-info" role="tabpanel"
                                 aria-labelledby="bank-card-info-tab">
                                <h3 class="card-header font-size-20">银行卡信息
                                    @if(count($user->bankCard)<2)
                                        <a class="btn btn-primary btn-outline float-right btn-sm"
                                           data-target="#addBankCard" data-toggle="modal">添加</a>
                                    @endif
                                </h3>
                                <h4>&nbsp;</h4>
                                <div class="font-size-fix card-deck">
                                    @if(count($user->bankCard))
                                        @foreach($user->bankCard as $key=> $bankcard)
                                            <div id="card1" class="card card-body card-overlay">
                                                <div>
                                                    <h3 class="font-size-20">
                                                        卡{{ $key+1 }}{{ $bankcard->bank_type==1?'(主卡)':'(副卡)' }}
                                                        @if($bankcard->bank_type!=1)
                                                            <button data-href="{{ route('users.delete_bank_card',['id' => $bankcard->id]) }}"
                                                                    class="btn btn-danger btn-outline float-right btn-sm delete_bank_card">
                                                                删除
                                                            </button>
                                                        @endif
                                                    </h3>
                                                </div>
                                                <div class="row">
                                                    @if($bankcard->card_num)
                                                        <div class="col-12 text-left">银行卡号：
                                                            {{ \App\Models\UserBankCard::formatBankCardShowType(decrypt_no_user_exception($bankcard->card_num,$bankcard->user_id),4) }}
                                                        </div>
                                                    @else
                                                        <div class="col-12 text-left">银行卡号：</div>
                                                    @endif

                                                    @if( $bankcard->bank)
                                                        <div class="col-12">
                                                            开户行： {{  decrypt_no_user_exception($bankcard->bank )  }}</div>
                                                    @else
                                                        <div class="col-12">开户行：</div>
                                                    @endif

                                                </div>
                                                <div class="row">
                                                    @if( $bankcard->branch_bank)
                                                        <div class="col-12 text-left">
                                                            支行名称： {{   decrypt_no_user_exception($bankcard->branch_bank)  }}</div>
                                                    @else
                                                        <div class="col-12 text-left">支行名称：</div>
                                                    @endif

                                                    @if($bankcard->bank_provinc||$bankcard->bank_city)
                                                        <div class="col-12">银行卡属地：
                                                            @php
                                                                if ($bankcard->bank_province && $bankcard->bank_city) {
                                                                    echo decrypt_no_user_exception($bankcard->bank_province).decrypt_no_user_exception($bankcard->bank_city);
                                                                } elseif($bankcard->bank_province && !$bankcard->bank_city) {
                                                                    echo decrypt_no_user_exception($bankcard->bank_province);
                                                                } elseif(!$bankcard->bank_province && $bankcard->bank_city){
                                                                    echo decrypt_no_user_exception($bankcard->bank_city);
                                                                }
                                                            @endphp
                                                        </div>
                                                    @else
                                                        <div class="col-md-6">银行卡属地：</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane fade" id="user-urgent-contact" role="tabpanel"
                                 aria-labelledby="user-urgent-contact-tab">
                                <h3 class="card-header font-size-20">紧急联系人 <a
                                            class="btn btn-primary btn-outline float-right btn-sm"
                                            data-target="#addLinkUser"
                                            data-toggle="modal">添加</a></h3>

                                <div class="card-body" style="margin-top: 5%">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-outline table-vcenter text-nowrap card-table font-size-fix">
                                            <thead>
                                            <tr>
                                                <th class="text-center">关系</th>
                                                <th class="text-center">姓名</th>
                                                <th class="text-center">电话</th>
                                                <th class="text-center">操作</th>
                                            </tr>
                                            @if(count($user->urgentUser))
                                                @foreach($user->urgentUser as $urgentUser)
                                                    <tr data-key="0">
                                                        <td class="text-center">
                                                            @php
                                                                echo $urgentUser->relate ? decrypt_no_user_exception($urgentUser->relate ) : "";
                                                            @endphp
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                echo $urgentUser->relate_name ? decrypt_no_user_exception($urgentUser->relate_name ) : "";
                                                            @endphp
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                echo $urgentUser->relate_phone ? decrypt_no_user_exception($urgentUser->relate_phone ) : "";
                                                            @endphp
                                                        </td>
                                                        <td class="text-center">
                                                            <button data-href="{{ route('users.delete_urgent_user',['id' => $urgentUser->id]) }}"
                                                                    class="btn btn-danger btn-outline  btn-sm delete_urgent_user">
                                                                删除
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="family-info" role="tabpanel"
                                 aria-labelledby="family-info-tab">
                                <h3 class="card-header font-size-20">家庭信息<a
                                            class="btn btn-primary btn-outline float-right btn-sm"
                                            data-target="#addFamily"
                                            data-toggle="modal">添加</a></h3>
                                <div class="card-body" style="margin-top: 5%">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-outline table-vcenter text-nowrap card-table font-size-fix">
                                            <thead>
                                            <tr>
                                                <th class="text-center">关系</th>
                                                <th class="text-center">姓名</th>
                                                <th class="text-center">性别</th>
                                                <th class="text-center">操作</th>
                                            </tr>
                                            @if(count($user->family))
                                                @foreach($user->family as $family)
                                                    <tr data-key="0">
                                                        <th class="text-center">
                                                            @php
                                                                echo $family->family_relate ? decrypt_no_user_exception($family->family_relate) : "";
                                                            @endphp
                                                        </th>
                                                        <th class="text-center">
                                                            @php
                                                                echo $family->family_name ? decrypt_no_user_exception($family->family_name) : "";
                                                            @endphp
                                                        </th>
                                                        <th class="text-center">
                                                            @php
                                                                if ($family->family_sex) {
                                                                    if (decrypt_no_user_exception($family->family_sex) == 1){
                                                                        echo '男';
                                                                    } elseif(decrypt_no_user_exception($family->family_sex) == 2){
                                                                        echo '女';
                                                                    } else {
                                                                        echo '未知';
                                                                    }
                                                                }

                                                            @endphp
                                                        </th>
                                                        <th class="text-center">
                                                            <button data-href="{{ route('users.delete_family',['id' => $family->id]) }}"
                                                                    class="btn btn-danger btn-outline  btn-sm delete_family_user">
                                                                删除
                                                            </button>
                                                        </th>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="upload-real-photo" role="tabpanel"
                                 aria-labelledby="upload-real-photo-tab">
                                <h3 class="card-header font-size-20">个人照片上传</h3>
                                <div class="card-body">
                                    <h5>该照片用于考勤打卡，请确保真实性!</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if($user->detail && $user->detail->pic_user)
                                                <img src="{{ $user->detail->pic_user }}" alt="">
                                                <img src="{{  $user->detail->pic_user }}" width="200"
                                                     class="rounded-circle img-thumbnail"
                                                     alt="">
                                            @else
                                                <img src="" alt="">
                                            @endif
                                        </div>
                                        <div style="margin-top: 5%">
                                            <form action="" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="file" class="file input_id" name="pic_user">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--添加银行卡model-->
                <div class="modal fade" id="addBankCard" role="dialog" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">添加银行卡</h4>
                            </div>
                            <div class="modal-body">
                                <form class='addBankCard' action="javascript:void(0);" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label"
                                               id="model_main_body">银行卡号<label class="text-danger">*</label></label>
                                        <input class="form-control" name="bank_card_num" id="bank_card_num"
                                               oninput="myFunction()"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_seal_type">开户行<label
                                                    class="text-danger">*</label></label>
                                        <select class="form-control" name="bank" id="user_family_sex">
                                            <option value="0">-请选择-</option>
                                            @foreach($banks as $key => $bank)
                                                <option value="{{$key}}">{{$bank}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label"
                                               id="model_seal_type">支行名称</label>
                                        <input class="form-control" name="bank_branch" id="bank_branch"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label"
                                               id="model_seal_type">银行卡属地(省)</label>
                                        <input class="form-control" name="bank_province" id="bank_province"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label"
                                               id="model_seal_type">银行卡属地(市)<label class="text-danger">*</label></label>
                                        <input class="form-control" name="bank_city" id="bank_city"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_seal_type">银行卡类型（只能添加一张主卡）<label
                                                    class="text-danger">*</label></label>
                                        <select class="form-control" name="bank_type" id="bank_type">
                                            <option value="2">副卡</option>
                                            <option value="1">主卡</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default btn-sm line-height-fix"
                                                data-dismiss="modal">
                                            取消
                                        </button>
                                        <button data-href="{{  route('users.add_bank_card')  }}"
                                                type="submit"
                                                class="btn btn-primary btn-sm line-height-fix save-bankcard">保存
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!--添加紧急联系人model-->
                <div class="modal fade" id="addLinkUser" role="dialog" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">添加紧急联系人</h4>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:void(0)" class='addUrgentUser' method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_main_body">关系<label
                                                    class="text-danger">*</label></label>
                                        <input class="form-control" name="user_relate" id="user_urgent_relate"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_seal_type">姓名<label
                                                    class="text-danger">*</label></label>
                                        <input class="form-control" name="user_name" id="user_urgent_name"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_seal_type">电话<label
                                                    class="text-danger">*</label></label>
                                        <input class="form-control" name="user_phone" id="user_urgent_phone"/>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default btn-sm line-height-fix"
                                                data-dismiss="modal">
                                            取消
                                        </button>
                                        <button data-href="{{  route('users.add_urgent_user')  }}"
                                                type="submit"
                                                class="btn btn-primary btn-sm line-height-fix add-urgent-user">保存
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--添加家庭信息model-->
                <div class="modal fade" id="addFamily" role="dialog" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">添加家庭信息</h4>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:void(0)" class="addFamilyInfo" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_main_body">关系<label
                                                    class="text-danger">*</label></label>
                                        <input class="form-control" name="user_relate" id="user_family_relate"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_seal_type">姓名<label
                                                    class="text-danger">*</label></label>
                                        <input class="form-control" name="user_name" id="user_family_name"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="control-label" id="model_seal_type">性别<label
                                                    class="text-danger">*</label></label>
                                        <select class="form-control" name="user_sex" id="user_family_sex">
                                            <option value=1>男</option>
                                            <option value=2>女</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default btn-sm line-height-fix"
                                                data-dismiss="modal">
                                            取消
                                        </button>
                                        <button data-href="{{  route('users.add_family')  }}"
                                                type="submit" class="btn btn-primary btn-sm line-height-fix add_family">
                                            保存
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>
        @endsection

        @section('javascript')
            <script>

                card_tab_children = $("#v-pills-tab").children();
                tab_content_children = $(".tab-content").children();
                card_tab_children.each(function () {
                    $(this).off('click').click(function () {
                        current_card_tab = $(this).attr('id');
                        window.localStorage.setItem('local_card_tab', current_card_tab);
                        tab_content_children.removeClass('active show');
                        if (current_card_tab == 'base-info-tab') {
                            current_tab_content = 'base-info';
                        } else if (current_card_tab == 'bank-card-info-tab') {
                            current_tab_content = 'bank-card-info';
                        } else if (current_card_tab == 'user-urgent-contact-tab') {
                            current_tab_content = 'user-urgent-contact';
                        } else if (current_card_tab == 'family-info-tab') {
                            current_tab_content = 'family-info';
                        } else if (current_card_tab == 'upload-real-photo-tab') {
                            current_tab_content = 'upload-real-photo';
                        }
                        $("#" + current_tab_content).addClass("active show");
                        window.localStorage.setItem('local_tab_content', current_tab_content);
                    });
                });


                $(function () {

                    var get_local_card_tab = window.localStorage.getItem('local_card_tab');
                    var get_local_tab_content = window.localStorage.getItem('local_tab_content');

                    if (get_local_card_tab && get_local_tab_content) {
                        $("#v-pills-tab").children().removeClass('active show');
                        $(".tab-content").children().removeClass('active show');
                        $("#" + get_local_card_tab).addClass('active show');
                        $("#" + get_local_tab_content).addClass('active show');
                    }


                });

                message = "{{  session('message')  }}";
                if (message) {
                    alert(message);
                    delete message;
                }

            </script>

            {{--添加银行卡--}}
            <script>
                // 表单提交拦截
                //        form.submit(function (e) {
                //            if ($('#bank_card_num').val() === '' || $('#bank').val() === '' || $('#bank_city').val() === '') {
                //                alert("请填写必要信息");
                //                return false;
                //            }
                //        });
                function myFunction() {
                    var x = document.getElementById("bank_card_num").value;
                    var x = x.replace(/\s/g, '').replace(/(\d{4})(?=\d)/g, "$1 ");
                    document.getElementById("bank_card_num").value = x;
                }

                $(function () {
                    $('.save-bankcard').on('click', function (e) {
                        e.preventDefault();
                        var data = $('.addBankCard').serialize();
                        callPostAjax($(this), data, null);
                    })
                })
            </script>
            {{--删除银行卡--}}
            <script>
                $(function () {
                    $('.delete_bank_card').on('click', function () {
                        if (confirm("确定删除银行卡？")) {
                        } else {
                            return false;
                        }
                        callPostAjax($(this), {});
                    })
                });
            </script>
            {{--删除紧急联系人--}}
            <script>
                $(function () {
                    $('.delete_urgent_user').on('click', function () {
                        if (confirm("确定删除紧急联系人？")) {
                        } else {
                            return false;
                        }
                        callPostAjax($(this), {});
                    })
                });
            </script>
            {{--  添加紧急联系人--}}
            <script>
                $(function () {
                    $('.add-urgent-user').on('click', function (e) {
                        if ($('#user_urgent_relate').val() === '' || $('#user_urgent_name').val() === '' || $('#user_urgent_phone').val() === '') {
                            alert("请填写必要信息");
                            return false;
                        }
                        e.preventDefault();
                        var data = $('.addUrgentUser').serialize();
                        callPostAjax($(this), data, null,);
                    })
                })
            </script>
            {{--添加家庭信息--}}
            <script>
                $(function () {
                    $('.add_family').on('click', function (e) {
                        if ($('#user_family_relate').val() === '' || $('#user_family_name').val() === '' || $('#user_family_sex').val() === '') {
                            alert("请填写必要信息");
                            return false;
                        }
                        e.preventDefault();
                        var data = $('.addFamilyInfo').serialize();
                        callPostAjax($(this), data, null,);
                    })
                })
            </script>
            {{--删除家庭信息--}}
            <script>
                $(function () {
                    $('.delete_family_user').on('click', function () {
                        if (confirm("确定删除家庭信息？")) {
                        } else {
                            return false;
                        }
                        callPostAjax($(this), {});
                    })
                });
            </script>

            {{--图片上传--}}
            <script src="{{  asset('js/fileinput/fileinput.js')  }}"></script>
            <script>
                $(".input_id").each(function () {
                    $(this).fileinput({
                        uploadUrl: '{{  route('users.uploadimg',['id' => $user->id])  }}',
                        enctype: 'multipart/form-data',
                        allowedFileExtensions: ['jpg', 'png', 'bmp', 'jpeg'],
                        uploadExtraData: {"_token": $('meta[name="csrf-token"]').attr('content')},
                    }).on("fileuploaded", function (event, data, previewId, index) {
                        if (data.response.status == "success") {
                            alert(data.response.messages);
                            location.reload(true);
                        } else {
                            alert(data.response.messages);
                        }
                    })
                });
            </script>

@endsection