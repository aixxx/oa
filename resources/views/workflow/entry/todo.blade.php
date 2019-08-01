@extends('layouts.main')
@section('head')
    <style>
        .entry_todo_list .card-body {
            display: none;
        }

        .entry_todo_list .handle_all {
            margin-right: 10px;
            display: none;
        }

        .entry_todo_list .detail_trigger {
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto"><h1>待办事项</h1></div>
            <div class="top-right">
                <a href="{{ route('workflow.entry.create') }}" class="btn btn-primary">发起申请</a>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid entry_todo_list">
        <div class="row">
            <div class="col-12">
                @if(!$list)
                    <h2 class="text-center">暂无待办事项</h2>
                @endif
                @foreach($list as $name=> $card)
                    <div class="card" id="{{$card['flow_no']}}">
                        <h5 class="card-header">
                            <a class="detail_trigger" data="{{$card['flow_no']}}">{{str_replace("流程","审批",str_replace("申请","审批",$name))}}（{{count
                            ($card['data'])
                            }}/{{$card['count']}}）</a>
                            {{--@if(count($card['data'])!=$card['count'])--}}
                            {{--<a href="{{route('workflow.entry.my_procs')}}" class="more">more>></a>--}}
                            {{--@endif--}}
                            <button class="unpass_all handle_all pull-right btn btn-sm btn-danger" data="{{$card['flow_no']}}"
                                    data-href="{{route("workflow.proc.unpass_all")}}">批量拒绝
                            </button>
                            <button class="pass_all handle_all pull-right btn btn-sm btn-success" data="{{$card['flow_no']}}"
                                    data-href="{{route("workflow.proc.pass_all")}}">批量同意
                            </button>
                        </h5>
                        <div class="card-body">
                            <table class="table v-align-middle">
                                <thead class="bg-light">
                                <tr>
                                    <th width="50px">
                                        <input value="0" type="checkbox" name="all_proc_id" data="{{$card['flow_no']}}" class="select_all"/>
                                    </th>
                                    <th>工号</th>
                                    <th>申请人</th>
                                    <th>所属部门</th>
                                    @if(in_array($card['flow_no'],[]))
                                        <th>流程标题</th>
                                    @endif
                                    <th>提交时间</th>
                                    @foreach($card['template_show'] as $template_show)
                                        <th>{{trim($template_show->field_name,'*')}}</th>
                                    @endforeach
                                    <th width="130px" class="text-center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($card['data'] as $entry)
                                    <tr id="{{"entry_".$entry->id}}">
                                        <td><input type="checkbox" name="proc_id" value="{{$entry->proc_id}}"/></td>
                                        <td>
                                            @php
                                            echo $entry->user->status == APP\Models\User::STATUS_PENDING_JOIN?'暂无':App\Models\User::getEmployeeNum($entry->user->employee_num);
                                            @endphp
                                        </td>
                                        <td>{{$entry->user->chinese_name}}</td>
                                        <td>
                                            @php
                                                if($entry->user->status == APP\Models\User::STATUS_PENDING_JOIN){
                                                    echo '暂无';
                                                }else{
                                                    $position = mb_strrpos($entry->user->primary_dept, '/');
                                                    $departName = mb_substr($entry->user->primary_dept, $position + 1, mb_strlen($entry->user->primary_dept) - 1);
                                                    echo sprintf('<span data-toggle="tooltip" data-placement="top" title="%s">%s</span>', $entry->user->primary_dept, $departName);
                                                }
                                            @endphp
                                        </td>
                                        <td>{{$entry->created_at}}</td>
                                        @foreach($card['template_show'] as $template_show)
                                            @if($template_show->field=='company_name_list')
                                                @php
                                                    $value=$entry->entry_data->where('field_name',$template_show->field)->first()->field_value;
                                                    $value=\App\Models\Company::find($value);
                                                    $fieldValueOrigin = trim($value->name??'点击详情查看');
                                                @endphp
                                            @else
                                                @php
                                                    $fieldValueOrigin = trim($entry->entry_data->where('field_name',$template_show->field)->first()->field_value ?? '点击详情查看');
                                                @endphp
                                            @endif
                                            @php
                                                $fieldWordsTooLong = mb_strlen($fieldValueOrigin) > 20;
                                                $fieldValue = $fieldWordsTooLong ? mb_substr($fieldValueOrigin, 0, 19) . '...' : ($fieldValueOrigin ??'点击详情查看');
                                            @endphp
                                            <td>@php echo $fieldWordsTooLong ? sprintf('<span data-toggle="tooltip" data-placement="top" title="%s">%s</span>', $fieldValueOrigin, $fieldValue) : $fieldValue; @endphp</td>
                                        @endforeach
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm pass"
                                                    data-href="/workflow/pass/{{$entry->proc_id}}" data-parent="{{"entry_".$entry->id}}">同意
                                            </button>
                                            <button type="button" class="btn btn-danger  btn-sm reject"
                                                    data-href="/workflow/reject/{{$entry->proc_id}}" data-parent="{{"entry_".$entry->id}}">驳回
                                            </button>
                                            <span><a href="{{route("workflow.proc.show",$entry->proc_id)}}">详情</a></span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script>
        function success(response) {
            if (response.status == 'success' && response.message != '') {
                alert(response.message);
            }
            if (response.code == '0') {
                window.location.reload();
            }
        }
        $('.pass').on('click', function () {
            if (!confirm("确认同意？")) {
                return;
            }
            var self = $(this);
            callPostAjax($(this), {
                content: ''
            }, function (response) {
                success(response);
                $("#" + self.attr('data-parent')).remove();
            });
        });
        $('.reject').on('click', function () {
            var content = prompt('请填写批复意见');
            if (!content) {
                if (content == '') {
                    alert('请填写批复意见')
                }
                return;
            }
            var self = $(this);
            callPostAjax($(this), {
                content: content
            }, function (response) {
                success(response);
                $("#" + self.attr('data-parent')).remove();
            });
        });
        $('.select_all').on('click', function () {
            $("#" + $(this).attr("data") + " input[type=checkbox]").prop("checked", this.checked);
        });
        $('.detail_trigger').on('click', function () {
            $(".entry_todo_list .card-body ").hide();
            $(".entry_todo_list .handle_all ").hide();
            $("input[type=checkbox]").prop("checked", false);
            $("#" + $(this).attr("data") + " .card-body").show(30);
            $("#" + $(this).attr("data") + " .handle_all").show();

        });
        $(".pass_all").on('click', function () {
            var check_box = $("#" + $(this).attr("data") + " input[type=checkbox]:checked");
            var ids = {};
            var checked = false;
            check_box.each(function (i, em) {
                if ($(em).val() > 0) {
                    ids[i] = $(em).val();
                    checked = true;
                }
            });
            if (!checked) {
                alert('未选中任何流程');
                return;
            }
            if (!confirm("确认批量同意所选中的流程？")) {
                return;
            }
            callPostAjax($(this), {
                ids: ids
            }, function (response) {
                success(response);
                window.location.reload();
            });
        });
        $(".unpass_all").on('click', function () {
            var check_box = $("#" + $(this).attr("data") + " input[type=checkbox]:checked");
            var ids = {};
            var checked = false;
            check_box.each(function (i, em) {
                if ($(em).val() > 0) {
                    ids[i] = $(em).val();
                    checked = true;
                }
            });
            if (!checked) {
                alert('未选中任何流程');
                return;
            }
            if (!confirm("确认批量拒绝所选中的流程？")) {
                return;
            }
            callPostAjax($(this), {
                ids: ids
            }, function (response) {
                success(response);
                window.location.reload();
            });
        });
        $(".detail_trigger").each(function (i, item) {
            if (i == 0) {
                $(item).trigger('click');
            }
        });
        $(function () {
            $("html,body").animate({scrollTop: 0}, 10);
        })
    </script>
@endsection
