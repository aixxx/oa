@extends('layouts.main')

@section('head')
    <style>
        p.form-info-p {
            border-bottom: 1px solid #eee;
        }

        .card-body label {
            font-weight: bold;
        }

        @media screen and (max-width: 576px) {
            .btn-sm.print {
                display: none;
            }

            .proc #pass, .proc #reject {
                width: 49%;
            }
        }
    </style>
@endsection

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto"><h1>{{$entry->user->chinese_name}}：{{$entry->title}} </h1></div>
        </div>
    </header>
    <section class="page-content container-fluid proc">
        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card">
                    <div class="form-group card-header">
                        <h3 class="pull-left" style="margin:7px 0 0 0;">
                            {{$entry->flow->flow_name}}
                        </h3>
                        <button class="print btn btn-success pull-right btn-sm">打印</button>
                    </div>
                    <div class="card-body">
                        {!! $form_html !!}
                        <hr class="dashed">
                        <div class="form-group">
                            <label>批复意见</label>
                            @if($proc->status == \App\Models\Workflow\Proc::STATUS_IN_HAND)
                                <textarea rows="3" class="form-control" placeholder="批复意见" name="content"></textarea>
                            @else
                                <p>{{$proc->content}}</p>
                            @endif
                        </div>
                        {{csrf_field()}}
                    </div>
                    <div class="card-footer">
                        @if($entry->status == \App\Models\Workflow\Entry::STATUS_IN_HAND)
                            @if($proc->status == \App\Models\Workflow\Proc::STATUS_IN_HAND)
                                <input type="hidden" name="proc_id" value="{{$proc->id}}">
                                <button type="button" class="btn btn-success" id="pass" data-href="/workflow/pass-next/{{$proc->id}}">同意</button>
                                <button type="button" class="btn btn-danger" id="reject" data-href="/workflow/reject-next/{{$proc->id}}">驳回</button>
                            @elseif($proc->status == \App\Models\Workflow\Proc::STATUS_PASSED)
                                <p>已审批,审批人:{{$proc->auditor_name}}</p>
                            @elseif($proc->status == \App\Models\Workflow\Proc::STATUS_REJECTED)
                                <p>已驳回,审批人:{{$proc->auditor_name}}</p>
                            @endif
                        @elseif($entry->status == \App\Models\Workflow\Entry::STATUS_CANCEL)
                            <p>申请人已撤销</p>
                        @elseif($entry->status == \App\Models\Workflow\Entry::STATUS_REJECTED)
                            <p>已驳回</p>
                        @elseif($entry->status == \App\Models\Workflow\Entry::STATUS_FINISHED)
                            <p>已完成</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-xxl-12">
                {!! $processes_html !!}
            </div>
        </div>

    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('#pass').on('click', function () {
                $(this).attr('disabled', true);
                $('#reject').attr('disabled', true);
                var content = $('textarea[name=content]').val();
                callPostAjax($(this), {
                    content: content
                },function (response) {
                        if (response.status == 'success') {
                            message_show_success(response.message,response.redirect);
                        }
                    }
                );
            });
            $('#reject').on('click', function () {
                $(this).attr('disabled', true);
                $('#pass').attr('disabled', true);
                var content = $('textarea[name=content]').val();
                if (content == '') {
                    message_show_error('请填写批复意见!');
                    $(this).removeAttr('disabled');
                    $('#pass').removeAttr('disabled');
                    return;
                }
                callPostAjax($(this), {
                    content: content
                },function (response) {
                    if (response.status == 'success') {
                        message_show_success(response.message,response.redirect);
                    }
                });
            });
        });
        $("label:contains('付款金额')").width('11.5%');
    </script>
@endsection
